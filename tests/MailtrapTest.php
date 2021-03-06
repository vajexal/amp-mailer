<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests;

use Amp\Delayed;
use Amp\Http\Client\HttpClientBuilder;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Mailer;
use Vajexal\AmpMailer\MailerBuilder;
use Vajexal\AmpMailer\Tests\Mailtrap\Inbox;
use Vajexal\AmpMailer\Tests\Mailtrap\MailtrapClient;
use Vajexal\AmpMailer\Tests\Mailtrap\Message;

class MailtrapTest extends TestCase
{
    private MailtrapClient $mailtrapClient;
    private Inbox          $inbox;
    private Mailer         $mailer;

    protected function setUp(): void
    {
        parent::setUp();

        if (!\getenv('MAILTRAP_API_TOKEN') || !(int) \getenv('MAILTRAP_TEST_INBOX_ID')) {
            $this->markTestSkipped('MAILTRAP_API_TOKEN and MAILTRAP_TEST_INBOX_ID env vars should be provided');
        }
    }

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

    public function testUnicode()
    {
        $mail = (new Mail)
            ->from('from@example.com', 'Отправитель')
            ->to('to@example.com', 'Получатель')
            ->subject('Тайтл')
            ->text('Тест');

        yield $this->mailer->send($mail);

        yield new Delayed(100);

        $messages = yield $this->mailtrapClient->getMessages($this->inbox);

        $this->assertCount(1, $messages);

        /** @var Message $message */
        $message = $messages[0];

        $this->assertEquals('from@example.com', $message->getFrom()->getEmail());
        $this->assertEquals('Отправитель', $message->getFrom()->getName());
        $this->assertEquals('to@example.com', $message->getTo()->getEmail());
        $this->assertEquals('Получатель', $message->getTo()->getName());
        $this->assertEquals('Тайтл', $message->getSubject());
        $this->assertEquals('Тест', yield $this->mailtrapClient->getMessageText($message));
    }
}
