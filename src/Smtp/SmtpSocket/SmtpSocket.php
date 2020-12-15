<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\SmtpSocket;

use Amp\Promise;
use Amp\Socket\EncryptableSocket;
use Amp\Socket\SocketException;
use Psr\Log\LoggerInterface;
use Vajexal\AmpMailer\Smtp\Exception\SmtpException;
use Vajexal\AmpMailer\Smtp\SmtpResponse;
use const Vajexal\AmpMailer\Smtp\SMTP_LINE_BREAK;
use function Amp\call;

abstract class SmtpSocket
{
    private EncryptableSocket $socket;
    private LoggerInterface   $logger;

    public function __construct(EncryptableSocket $socket, LoggerInterface $logger)
    {
        $this->socket = $socket;
        $this->logger = $logger;
    }

    /**
     * @param string $message
     * @param int[] $expectedCodes
     * @return Promise<SmtpResponse>
     */
    abstract public function send(string $message, array $expectedCodes): Promise;

    protected function write(string $message): Promise
    {
        $this->logger->debug('C: ' . $message);

        return $this->socket->write($message . SMTP_LINE_BREAK);
    }

    protected function read(): Promise
    {
        return call(function () {
            $response = yield $this->socket->read();

            $this->logger->debug('S: ' . $response);

            return $response;
        });
    }

    public function encrypt(): Promise
    {
        try {
            return $this->socket->setupTls();
        } catch (SocketException $e) {
            throw SmtpException::unableToSetupTls($e);
        }
    }
}
