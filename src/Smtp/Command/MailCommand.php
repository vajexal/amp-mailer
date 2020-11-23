<?php

namespace Vajexal\AmpMailer\Smtp\Command;

use Vajexal\AmpMailer\Exception\MailException;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Encoder\Email\EmailEncoder;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket;

class MailCommand implements Command
{
    private EmailEncoder $encoder;

    public function __construct(EmailEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        if (!$mail->getFrom()) {
            throw MailException::emptyFrom();
        }

        $email = $this->encoder->encode($mail->getFrom()->getEmail());

        yield $socket->send(sprintf('MAIL FROM:<%s>', $email), [250]);
    }
}
