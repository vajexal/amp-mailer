<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests;

use Amp\Socket\SocketAddress;
use Vajexal\AmpMailer\Exception\EmailException;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Mailer;
use Vajexal\AmpMailer\MailerBuilder;
use Vajexal\AmpMailer\Smtp\ConnectionConfig;
use Vajexal\AmpMailer\Smtp\Exception\SmtpException;
use Vajexal\AmpMailer\Smtp\SmtpDriver;

class MailerValidationTest extends TestCase
{
    private DumpSmtpServer $smtpServer;
    private SocketAddress  $address;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setTimeout(2000);

        $this->smtpServer = new DumpSmtpServer;
        $this->address    = $this->smtpServer->start();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->smtpServer->stop();
    }

    public function invalidEmailPartsProvider()
    {
        return [
            ['ъ@example.com', 'Non-ASCII characters are not supported in local part of'],
            [\sprintf('foo@%s.com', \str_repeat('ъ', 60)), 'Invalid domain part of'],
        ];
    }

    /**
     * @dataProvider invalidEmailPartsProvider
     */
    public function testInvalidEmailParts(string $email, string $expectedExceptionMessage)
    {
        $this->expectException(EmailException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $connectionConfig = new ConnectionConfig($this->address->getHost(), $this->address->getPort());
        $driver           = (new SmtpDriver($connectionConfig))->setTlsRequired(false);

        $mailer = new Mailer($driver);

        $mail = (new Mail)
            ->from($email)
            ->to('from@example.com')
            ->subject('Test')
            ->text('Test');

        yield $mailer->send($mail);
    }

    public function testTlsIsRequired()
    {
        $this->expectException(SmtpException::class);
        $this->expectExceptionMessage('TLS is required but server does not support it');

        $mailer = (new MailerBuilder)
            ->host($this->address->getHost())
            ->port($this->address->getPort())
            ->build();

        $mail = (new Mail)
            ->from('from@example.com')
            ->to('to@example.com')
            ->subject('Test')
            ->text('Test');

        yield $mailer->send($mail);
    }
}
