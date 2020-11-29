<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp;

use Amp\Promise;
use Amp\Socket\ClientTlsContext;
use Amp\Socket\ConnectContext;
use Amp\Socket\EncryptableSocket;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Vajexal\AmpMailer\DiLocator;
use Vajexal\AmpMailer\Driver;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Exception\SmtpException;
use function Amp\call;
use function Amp\Socket\connect;

class SmtpDriver implements Driver
{
    const MIME_MAX_LINE_LENGTH = 76;
    const MAX_BOUNDARY_LENGTH  = 70;
    const LB                   = "\r\n";

    private ConnectionConfig $connectionConfig;
    private LoggerInterface  $logger;
    private bool             $isTlsRequired = true;

    public function __construct(ConnectionConfig $connectionConfig, LoggerInterface $logger = null)
    {
        $this->connectionConfig = $connectionConfig;
        $this->logger           = $logger ?: new NullLogger;
    }

    public function send(Mail $mail): Promise
    {
        return call(function () use ($mail) {
            $tcpSocket = yield $this->openTcpSocket();
            $socket    = new SmtpSocket($tcpSocket, $this->logger);
            $server    = new SmtpServer($this->connectionConfig);
            $executor  = new CommandExecutor($socket, $server, $mail);

            yield $this->startSession($executor, $server);

            $to  = $mail->getTo();
            $bcc = $mail->getBcc();

            $mail->setBcc([]);

            if ($mail->getTo()) {
                yield $this->performSend($executor);
            }

            foreach ($bcc as $address) {
                $mail->setTo([$address]);
                $mail->setBcc([$address]);

                yield $this->performSend($executor);
            }

            $mail->setBcc($bcc);
            $mail->setTo($to);

            yield $executor->execute(DiLocator::quitCommand());

            $tcpSocket->close();
        });
    }

    private function performSend(CommandExecutor $executor): Promise
    {
        return call(function () use ($executor) {
            yield $executor->execute(DiLocator::mailCommand());
            yield $executor->execute(DiLocator::recipientCommand());
            yield $executor->execute(DiLocator::dataCommand());
        });
    }

    private function openTcpSocket(): Promise
    {
        return call(function () {
            $connectContext = (new ConnectContext)
                ->withTlsContext(new ClientTlsContext($this->connectionConfig->getHost()));

            /** @var EncryptableSocket $tcpSocket */
            $tcpSocket = yield connect(\sprintf('%s:%s', $this->connectionConfig->getHost(), $this->connectionConfig->getPort()), $connectContext);

            $response = yield $tcpSocket->read(); // greetings

            $this->logger->debug('S: ' . $response);

            return $tcpSocket;
        });
    }

    private function startSession(CommandExecutor $executor, SmtpServer $server): Promise
    {
        return call(function () use ($executor, $server) {
            try {
                yield $executor->execute(DiLocator::ehloCommand());
            } catch (SmtpException $e) {
                yield $executor->execute(DiLocator::heloCommand());
            }

            if ($this->isTlsRequired && !$server->supportsTls()) {
                throw SmtpException::tlsRequired();
            }

            if ($server->supportsTls()) {
                yield $executor->execute(DiLocator::startTlsCommand());
                yield $executor->execute(DiLocator::ehloCommand());
            }

            if ($server->supportsAuth()) {
                yield $executor->execute(DiLocator::authCommand());
            }
        });
    }

    public function setTlsRequired(bool $required = true): self
    {
        $this->isTlsRequired = $required;

        return $this;
    }
}
