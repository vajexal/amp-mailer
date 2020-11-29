<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Auth;

use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket;

class PlainAuthStrategy implements AuthStrategy
{
    public function getPriority(): int
    {
        return 1;
    }

    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        yield $socket->send('AUTH LOGIN', [334]);

        $connectionConfig = $server->getConnectionConfig();

        yield $socket->send(\base64_encode(\sprintf("\0%s\0%s", $connectionConfig->getUsername(), $connectionConfig->getPassword())), [235]);
    }
}
