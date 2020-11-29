<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp;

use Amp\Promise;
use Amp\Socket\EncryptableSocket;
use Psr\Log\LoggerInterface;
use Vajexal\AmpMailer\Smtp\Exception\SmtpException;
use function Amp\call;

class SmtpSocket
{
    private EncryptableSocket $socket;
    private LoggerInterface   $logger;

    public function __construct(EncryptableSocket $socket, LoggerInterface $logger)
    {
        $this->socket = $socket;
        $this->logger = $logger;
    }

    public function send(string $message, array $expectedCodes): Promise
    {
        return call(function () use ($message, $expectedCodes) {
            $this->logger->debug('C: ' . $message);
            yield $this->socket->write($message . SmtpDriver::LB);

            $response = yield $this->socket->read();
            $this->logger->debug('S: ' . $response);

            $code    = (int) \substr($response, 0, 3);
            $content = \trim(\substr($response, 4));

            if (!\in_array($code, $expectedCodes, true)) {
                throw new SmtpException($response, $code);
            }

            return new SmtpResponse($code, $content);
        });
    }

    public function encrypt(): Promise
    {
        return $this->socket->setupTls();
    }
}
