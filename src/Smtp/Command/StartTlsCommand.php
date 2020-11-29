<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command;

use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket;

class StartTlsCommand implements Command
{
    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        yield $socket->send('STARTTLS', [220]);
        yield $socket->encrypt();
    }
}
