<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests;

use Vajexal\AmpMailer\Exception\EmailException;
use Vajexal\AmpMailer\Exception\MailException;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Mailer;

class MailTest extends TestCase
{
    private Mailer $mailer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mailer = new Mailer(new EmptyDriver);

        $this->setTimeout(2000);
    }

    public function testInvalidEmail()
    {
        $this->expectException(EmailException::class);
        $this->expectExceptionMessage('Email hack is invalid');

        $mail = (new Mail)
            ->from('foo@example.com')
            ->to('hack')
            ->text('Test');

        yield $this->mailer->send($mail);
    }

    public function testEmptyRecipients()
    {
        $this->expectException(MailException::class);
        $this->expectExceptionMessage('Empty recipients');

        $mail = (new Mail)
            ->from('foo@example.com')
            ->text('Test');

        yield $this->mailer->send($mail);
    }

    public function testEmptyBody()
    {
        $this->expectException(MailException::class);
        $this->expectExceptionMessage('Mail has no content');

        $mail = (new Mail)
            ->from('foo@example.com')
            ->to('bar@example.com');

        yield $this->mailer->send($mail);
    }

    public function testEmptyFrom()
    {
        $this->expectException(MailException::class);
        $this->expectExceptionMessage('Empty from');

        $mail = (new Mail)
            ->to('bar@example.com')
            ->text('Test');

        yield $this->mailer->send($mail);
    }
}
