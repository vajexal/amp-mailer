<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests;

use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Mailer;
use Vajexal\AmpMailer\Smtp\ConnectionConfig;
use Vajexal\AmpMailer\Smtp\SmtpDriver;

class MailerTest extends TestCase
{
    private DumpSmtpServer $smtpServer;
    private Mailer         $mailer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setTimeout(2000);

        $this->smtpServer = new DumpSmtpServer;
        $address          = $this->smtpServer->start();

        $connectionConfig = new ConnectionConfig($address->getHost(), $address->getPort());
        $driver           = (new SmtpDriver($connectionConfig))->setTlsRequired(false);

        $this->mailer = new Mailer($driver);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->smtpServer->stop();
    }

    public function testSendingEmail()
    {
        $mail = (new Mail)
            ->from('foo@example.com')
            ->to('bar@example.com')
            ->subject('Test')
            ->text('Test');

        yield $this->mailer->send($mail);

        $this->assertOutputMatchesPattern(
            "EHLO [\w.]+\r
MAIL FROM:<foo@example.com>\r
RCPT TO:<bar@example.com>\r
DATA\r
Date: \w{3}, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} \+\d{4}\r
From: foo@example.com\r
To: bar@example.com\r
Message-ID: <[\w.]+@[\w.]+>\r
Subject: Test\r
MIME-Version: 1.0\r
Content-Type: text/plain; charset=utf-8\r
Content-Transfer-Encoding: base64\r
\r
VGVzdA==\r
.\r
QUIT\r
",
            $this->smtpServer->getContent()
        );
    }

    public function testFewRecipients()
    {
        $mail = (new Mail)
            ->from('foo1@example.com')
            ->replyTo('foo2@example.com', 'foo2')
            ->to('bar1@example.com', 'bar1')
            ->cc('baz1@example.com', 'baz1')
            ->cc('baz2@example.com')
            ->subject('Test')
            ->text('Test');

        yield $this->mailer->send($mail);

        $this->assertOutputMatchesPattern(
            "EHLO [\w.]+\r
MAIL FROM:<foo1@example.com>\r
RCPT TO:<bar1@example.com>\r
DATA\r
Date: \w{3}, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} \+\d{4}\r
From: foo1@example.com\r
Reply-To: foo2 <foo2@example.com>\r
To: bar1 <bar1@example.com>\r
Cc: baz1 <baz1@example.com>,\r
 baz2@example.com\r
Message-ID: <[\w.]+@[\w.]+>\r
Subject: Test\r
MIME-Version: 1.0\r
Content-Type: text/plain; charset=utf-8\r
Content-Transfer-Encoding: base64\r
\r
VGVzdA==\r
.\r
QUIT\r
",
            $this->smtpServer->getContent()
        );
    }

    public function testBcc()
    {
        $mail = (new Mail)
            ->from('foo@example.com')
            ->bcc('bar@example.com')
            ->bcc('baz@example.com')
            ->subject('Test')
            ->text('Test');

        yield $this->mailer->send($mail);

        $this->assertOutputMatchesPattern(
            "EHLO [\w.]+\r
MAIL FROM:<foo@example.com>\r
RCPT TO:<bar@example.com>\r
DATA\r
Date: \w{3}, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} \+\d{4}\r
From: foo@example.com\r
To: bar@example.com\r
Bcc: bar@example.com\r
Message-ID: <[\w.]+@[\w.]+>\r
Subject: Test\r
MIME-Version: 1.0\r
Content-Type: text/plain; charset=utf-8\r
Content-Transfer-Encoding: base64\r
\r
VGVzdA==\r
.\r
MAIL FROM:<foo@example.com>\r
RCPT TO:<baz@example.com>\r
DATA\r
Date: \w{3}, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} \+\d{4}\r
From: foo@example.com\r
To: baz@example.com\r
Bcc: baz@example.com\r
Message-ID: <[\w.]+@[\w.]+>\r
Subject: Test\r
MIME-Version: 1.0\r
Content-Type: text/plain; charset=utf-8\r
Content-Transfer-Encoding: base64\r
\r
VGVzdA==\r
.\r
QUIT\r
",
            $this->smtpServer->getContent()
        );
    }
}
