<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp;

use Amp\Promise;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Command\Command;
use function Amp\call;

class CommandExecutor
{
    private SmtpSocket $socket;
    private SmtpServer $server;
    private Mail       $mail;

    public function __construct(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        $this->socket = $socket;
        $this->server = $server;
        $this->mail   = $mail;
    }

    public function execute(Command $command): Promise
    {
        return call([$command, 'execute'], $this->socket, $this->server, $this->mail);
    }
}
