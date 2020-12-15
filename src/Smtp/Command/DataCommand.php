<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command;

use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\MailMessageBuilder;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket\SmtpSocket;

class DataCommand implements Command
{
    public const COMMAND = 'DATA';

    private MailMessageBuilder $mailMessageBuilder;

    public function __construct(MailMessageBuilder $mailMessageBuilder)
    {
        $this->mailMessageBuilder = $mailMessageBuilder;
    }

    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        yield $socket->send(self::COMMAND, [354]);

        $message = $mail->getRawMessage() ?: $this->mailMessageBuilder->build($mail);

        yield $socket->send($message, [250]);
    }
}
