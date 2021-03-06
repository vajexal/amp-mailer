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
use Vajexal\AmpMailer\Smtp\SmtpSocket\SimpleSmtpSocket;
use Vajexal\AmpMailer\Smtp\SmtpSocket\SmtpPipelinedSocket;
use function Amp\call;
use function Amp\Socket\connect;

class SmtpDriver implements Driver
{
    private ConnectionConfig $connectionConfig;
    private LoggerInterface  $logger;
    private bool             $isTlsRequired = true;

    public function __construct(ConnectionConfig $connectionConfig, LoggerInterface $logger = null)
    {
        $this->connectionConfig = $connectionConfig;
        $this->logger           = $logger ?: new NullLogger;
    }

    public function send(iterable $mails): Promise
    {
        return call(function () use ($mails) {
            $tcpSocket = yield $this->openTcpSocket();
            $socket    = new SimpleSmtpSocket($tcpSocket, $this->logger);
            $server    = new SmtpServer($this->connectionConfig);
            $executor  = new CommandExecutor($socket, $server, new Mail);

            yield $this->startSession($executor, $server);

            if ($server->supportsPipelining()) {
                $socket = new SmtpPipelinedSocket($tcpSocket, $this->logger);
            }

            /** @var Mail $mail */
            foreach ($mails as $mail) {
                $mail = clone $mail; // hope html property is not too big

                $bcc = $mail->getBcc();

                $mail->setBcc([]);

                $executor = new CommandExecutor($socket, $server, $mail);

                if ($mail->getTo()) {
                    yield $this->performSend($executor, $mail);
                }

                foreach ($bcc as $address) {
                    $mail->setBcc([$address]);

                    yield $this->performSend($executor, $mail);
                }
            }

            yield $executor->execute(DiLocator::quitCommand());

            $tcpSocket->close();
        });
    }

    private function performSend(CommandExecutor $executor, Mail $mail): Promise
    {
        return call(function () use ($executor, $mail) {
            $mailMessageBuilder = DiLocator::mailMessageBuilder();

            $mail->rawMessage($mailMessageBuilder->build($mail));

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

            if ($server->supportsAuth() && $this->connectionConfig->hasCredentials()) {
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
