<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests\Integration;

use Amp\Delayed;
use Amp\Http\Client\HttpClientBuilder;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Mailer;
use Vajexal\AmpMailer\MailerBuilder;
use Vajexal\AmpMailer\Smtp\Exception\SmtpException;
use Vajexal\AmpMailer\Tests\Integration\Mailtrap\Inbox;
use Vajexal\AmpMailer\Tests\Integration\Mailtrap\MailtrapClient;
use Vajexal\AmpMailer\Tests\Integration\Mailtrap\Message;
use Vajexal\AmpMailer\Tests\TestCase;

class MailerTest extends TestCase
{
    private MailtrapClient $mailtrapClient;
    private Inbox          $inbox;
    private Mailer         $mailer;

    protected function setUpAsync()
    {
        parent::setUpAsync();

        $this->mailtrapClient = new MailtrapClient(HttpClientBuilder::buildDefault(), \getenv('MAILTRAP_API_TOKEN'));
        $this->inbox          = yield $this->mailtrapClient->getInbox((int) \getenv('MAILTRAP_TEST_INBOX_ID'));

        yield $this->mailtrapClient->clearInbox($this->inbox);

        $this->mailer = (new MailerBuilder)
            ->host($this->inbox->getHost())
            ->port($this->inbox->getPort())
            ->username($this->inbox->getUsername())
            ->password($this->inbox->getPassword())
            ->build();
    }

    public function testAuthIsRequired()
    {
        $this->expectException(SmtpException::class);
        $this->expectExceptionMessage('Authentication required');

        $mailer = (new MailerBuilder)
            ->host($this->inbox->getHost())
            ->port($this->inbox->getPort())
            ->build();

        $mail = (new Mail)
            ->from('foo@example.com')
            ->to('bar@example.com')
            ->subject('Test')
            ->text('Test');

        yield $mailer->send($mail);
    }

    public function testUnicode()
    {
        $mail = (new Mail)
            ->from('foo@example.com', 'Иванов')
            ->to('bar@example.com', 'Петров')
            ->subject('Тайтл')
            ->text('Тест');

        yield $this->mailer->send($mail);

        yield new Delayed(100);

        $messages = yield $this->mailtrapClient->getMessages($this->inbox);

        $this->assertCount(1, $messages);

        /** @var Message $message */
        $message = $messages[0];

        $this->assertEquals('foo@example.com', $message->getFrom()->getEmail());
        $this->assertEquals('Иванов', $message->getFrom()->getName());
        $this->assertEquals('bar@example.com', $message->getTo()->getEmail());
        $this->assertEquals('Петров', $message->getTo()->getName());
        $this->assertEquals('Тайтл', $message->getSubject());
        $this->assertEquals('Тест', yield $this->mailtrapClient->getMessageText($message));
    }
}
