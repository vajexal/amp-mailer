<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command;

use Vajexal\AmpMailer\HostDetector;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket\SmtpSocket;

class HeloCommand implements Command
{
    public const COMMAND = 'HELO';

    private HostDetector $hostDetector;

    public function __construct(HostDetector $hostDetector)
    {
        $this->hostDetector = $hostDetector;
    }

    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        yield $socket->send(\sprintf('%s %s', self::COMMAND, $this->hostDetector->getHost()), [250]);
    }
}
