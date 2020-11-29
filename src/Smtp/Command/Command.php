<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command;

use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Exception\SmtpException;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket;

interface Command
{
    /**
     * @param SmtpSocket $socket
     * @param SmtpServer $server
     * @param Mail $mail
     * @return mixed
     * @throws SmtpException
     */
    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail);
}
