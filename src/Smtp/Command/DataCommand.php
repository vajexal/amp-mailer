<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command;

use Generator;
use Vajexal\AmpMailer\DiLocator;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Command\Header\Header;
use Vajexal\AmpMailer\Smtp\Encoder\Email\EmailEncoder;
use Vajexal\AmpMailer\Smtp\Encoder\Header\HeaderEncoder;
use Vajexal\AmpMailer\Smtp\Mime\MimeBuilder;
use Vajexal\AmpMailer\Smtp\SmtpDriver;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket;

class DataCommand implements Command
{
    private EmailEncoder  $emailEncoder;
    private HeaderEncoder $headerEncoder;
    private MimeBuilder   $mimeBuilder;

    public function __construct(EmailEncoder $emailEncoder, HeaderEncoder $headerEncoder, MimeBuilder $mimeBuilder)
    {
        $this->emailEncoder  = $emailEncoder;
        $this->headerEncoder = $headerEncoder;
        $this->mimeBuilder   = $mimeBuilder;
    }

    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        yield $socket->send('DATA', [354]);

        $message = '';

        /** @var Header $header */
        foreach ($this->getHeaders($mail) as $header) {
            $message .= $header->get($mail) . SmtpDriver::LB;
        }

        $message .= $this->mimeBuilder->build($mail)->getBody();
        $message .= '.';

        yield $socket->send($message, [250]); // todo max body size
    }

    private function getHeaders(Mail $mail): Generator
    {
        yield DiLocator::dateHeader();
        yield DiLocator::fromHeader();

        if ($mail->getReplyTo()) {
            yield DiLocator::replyToHeader();
        }

        yield DiLocator::toHeader();

        if ($mail->getCc()) {
            yield DiLocator::ccHeader();
        }

        if ($mail->getBcc()) {
            yield DiLocator::bccHeader();
        }

        yield DiLocator::messageIdHeader();

        if ($mail->getSubject()) {
            yield DiLocator::subjectHeader();
        }

        yield DiLocator::mimeVersionHeader();
    }
}
