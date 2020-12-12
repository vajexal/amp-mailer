<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp;

use Generator;
use Vajexal\AmpMailer\DiLocator;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Command\Header\Header;
use Vajexal\AmpMailer\Smtp\Mime\MimeBuilder;

class MailMessageBuilder
{
    private MimeBuilder $mimeBuilder;

    public function __construct(MimeBuilder $mimeBuilder)
    {
        $this->mimeBuilder = $mimeBuilder;
    }

    public function build(Mail $mail): string
    {
        $message = '';

        /** @var Header $header */
        foreach ($this->getHeaders($mail) as $header) {
            $message .= $header->get($mail) . SMTP_LINE_BREAK;
        }

        $message .= $this->mimeBuilder->build($mail)->getBody();
        $message .= '.';

        return $message;
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
