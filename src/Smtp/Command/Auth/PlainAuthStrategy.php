<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Auth;

use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Command\AuthCommand;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket\SmtpSocket;

class PlainAuthStrategy implements AuthStrategy
{
    public function getPriority(): int
    {
        return 1;
    }

    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        yield $socket->send(\sprintf('%s PLAIN', AuthCommand::COMMAND), [334]);

        $connectionConfig = $server->getConnectionConfig();

        $credentials = \base64_encode(\sprintf("\0%s\0%s", $connectionConfig->getUsername(), $connectionConfig->getPassword()));

        yield $socket->send($credentials, [235]);
    }
}
