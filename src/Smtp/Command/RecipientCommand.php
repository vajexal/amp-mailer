<?php

namespace Vajexal\AmpMailer\Smtp\Command;

use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Encoder\Email\EmailEncoder;
use Vajexal\AmpMailer\Smtp\Exception\SmtpException;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket;

class RecipientCommand implements Command
{
    private EmailEncoder $encoder;

    public function __construct(EmailEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        if (!$mail->getTo()) {
            throw SmtpException::emptyRecipient();
        }

        foreach ($mail->getTo() as $address) {
            $email = $this->encoder->encode($address->getEmail());

            yield $socket->send(\sprintf('RCPT TO:<%s>', $email), [250, 251]);
        }
    }
}
