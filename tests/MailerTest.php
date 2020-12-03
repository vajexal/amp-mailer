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
            ->from('from@example.com')
            ->replyTo('reply@example.com', 'reply')
            ->to('to@example.com', 'to')
            ->cc('cc1@example.com', 'cc1')
            ->cc('cc2@example.com')
            ->subject('Test')
            ->text('Test');

        yield $this->mailer->send($mail);

        $this->assertOutputMatchesPattern(
            "EHLO [\w.]+\r
MAIL FROM:<from@example.com>\r
RCPT TO:<to@example.com>\r
RCPT TO:<cc1@example.com>\r
RCPT TO:<cc2@example.com>\r
DATA\r
Date: \w{3}, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} \+\d{4}\r
From: from@example.com\r
Reply-To: reply <reply@example.com>\r
To: to <to@example.com>\r
Cc: cc1 <cc1@example.com>,\r
 cc2@example.com\r
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
            ->from('from@example.com')
            ->to('to@example.com')
            ->replyTo('reply@example.com')
            ->cc('cc@example.com')
            ->bcc('bcc1@example.com')
            ->bcc('bcc2@example.com')
            ->subject('Test')
            ->text('Test');

        yield $this->mailer->send($mail);

        $this->assertOutputMatchesPattern(
            "EHLO [\w.]+\r
MAIL FROM:<from@example.com>\r
RCPT TO:<to@example.com>\r
RCPT TO:<cc@example.com>\r
DATA\r
Date: \w{3}, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} \+\d{4}\r
From: from@example.com\r
Reply-To: reply@example.com\r
To: to@example.com\r
Cc: cc@example.com\r
Message-ID: <[\w.]+@[\w.]+>\r
Subject: Test\r
MIME-Version: 1.0\r
Content-Type: text/plain; charset=utf-8\r
Content-Transfer-Encoding: base64\r
\r
VGVzdA==\r
.\r
MAIL FROM:<from@example.com>\r
RCPT TO:<bcc1@example.com>\r
DATA\r
Date: \w{3}, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} \+\d{4}\r
From: from@example.com\r
Reply-To: reply@example.com\r
To: to@example.com\r
Cc: cc@example.com\r
Bcc: bcc1@example.com\r
Message-ID: <[\w.]+@[\w.]+>\r
Subject: Test\r
MIME-Version: 1.0\r
Content-Type: text/plain; charset=utf-8\r
Content-Transfer-Encoding: base64\r
\r
VGVzdA==\r
.\r
MAIL FROM:<from@example.com>\r
RCPT TO:<bcc2@example.com>\r
DATA\r
Date: \w{3}, \d{2} \w{3} \d{4} \d{2}:\d{2}:\d{2} \+\d{4}\r
From: from@example.com\r
Reply-To: reply@example.com\r
To: to@example.com\r
Cc: cc@example.com\r
Bcc: bcc2@example.com\r
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
