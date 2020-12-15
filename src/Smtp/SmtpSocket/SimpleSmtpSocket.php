<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\SmtpSocket;

use Amp\Promise;
use Vajexal\AmpMailer\Smtp\Exception\SmtpException;
use Vajexal\AmpMailer\Smtp\SmtpResponse;
use const Vajexal\AmpMailer\Smtp\RESPONSE_CODE_LENGTH;
use function Amp\call;

class SimpleSmtpSocket extends SmtpSocket
{
    public function send(string $message, array $expectedCodes): Promise
    {
        return call(function () use ($message, $expectedCodes) {
            yield $this->write($message);

            $response = yield $this->read();

            $code    = (int) \substr($response, 0, RESPONSE_CODE_LENGTH);
            $content = \trim(\substr($response, RESPONSE_CODE_LENGTH + 1));

            if (!\in_array($code, $expectedCodes, true)) {
                throw SmtpException::unexpectedResponse($response);
            }

            return new SmtpResponse($code, $content);
        });
    }
}
