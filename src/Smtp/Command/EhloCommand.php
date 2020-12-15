<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command;

use Vajexal\AmpMailer\DiLocator;
use Vajexal\AmpMailer\HostDetector;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Command\Extension\Extension;
use Vajexal\AmpMailer\Smtp\SmtpResponse;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket\SmtpSocket;

class EhloCommand implements Command
{
    public const COMMAND = 'EHLO';

    private HostDetector $hostDetector;
    /** @var Extension[] */
    private array $extensions;

    public function __construct(HostDetector $hostDetector)
    {
        $this->hostDetector = $hostDetector;
        $this->extensions   = [
            DiLocator::authExtension(),
            DiLocator::tlsExtension(),
            DiLocator::sizeExtension(),
            DiLocator::pipeliningExtension(),
        ];
    }

    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        /** @var SmtpResponse $response */
        $response = yield $socket->send(\sprintf('%s %s', self::COMMAND, $this->hostDetector->getHost()), [250]);

        foreach ($this->extensions as $extension) {
            $extension->check($response->getContent(), $server);
        }
    }
}
