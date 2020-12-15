<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\SmtpSocket;

use Amp\Promise;
use Vajexal\AmpMailer\Smtp\Command\MailCommand;
use Vajexal\AmpMailer\Smtp\Command\RecipientCommand;
use Vajexal\AmpMailer\Smtp\Exception\SmtpException;
use Vajexal\AmpMailer\Smtp\SmtpRequest;
use Vajexal\AmpMailer\Smtp\SmtpResponse;
use const Vajexal\AmpMailer\Smtp\RESPONSE_CODE_LENGTH;
use function Amp\call;

class SmtpPipelinedSocket extends SmtpSocket
{
    private const PIPELINED_COMMANDS = [
        MailCommand::COMMAND,
        RecipientCommand::COMMAND,
    ];

    /** @var SmtpRequest[] */
    private array $buffer = [];

    public function send(string $message, array $expectedCodes): Promise
    {
        return call(function () use ($message, $expectedCodes) {
            yield $this->write($message);

            $this->buffer[] = new SmtpRequest($message, $expectedCodes);

            foreach (self::PIPELINED_COMMANDS as $command) {
                if (\strpos($message, $command) === 0) {
                    return new SmtpResponse($expectedCodes[0] ?? 0, '');
                }
            }

            $response = null;

            foreach ($this->buffer as $request) {
                $response = yield $this->read();

                $code    = (int) \substr($response, 0, RESPONSE_CODE_LENGTH);
                $content = \trim(\substr($response, RESPONSE_CODE_LENGTH + 1));

                if (!\in_array($code, $request->getExpectedCodes(), true)) {
                    throw SmtpException::unexpectedResponse($response);
                }

                $response = new SmtpResponse($code, $content);
            }

            $this->buffer = [];

            return $response;
        });
    }
}
