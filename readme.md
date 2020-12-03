Sending mail using SMTP and [amphp](https://amphp.org)

### Installation

```bash
composer require vajexal/amp-mailer:dev-master
```

### Usage

```php
<?php

declare(strict_types=1);

use Amp\Loop;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\MailerBuilder;

require_once 'vendor/autoload.php';

Loop::run(function () {
    $mailer = (new MailerBuilder)
        ->host('...')
        ->port(2525)
        ->username('...')
        ->password('...')
        ->build();

    $mail = (new Mail)
        ->from('from@example.com')
        ->to('to@example.com')
        ->subject('Test')
        ->text('Test');

    yield $mailer->send($mail);
});
```

#### More complex mail

```php
use Vajexal\AmpMailer\Mail;

$mail = (new Mail)
    ->from('from@example.com', 'from') // email and username
    ->replyTo('reply1@example.com', 'reply1')
    ->replyTo('reply2@example.com')
    ->to('to1@example.com', 'to1')
    ->to('to2@example.com')
    ->cc('cc1@example.com', 'cc1')
    ->cc('cc2@example.com')
    ->bcc('bcc1@example.com', 'bcc1')
    ->bcc('bcc2@example.com')
    ->subject('Test')
    ->text('Test')
    ->html('<b>Test</b>');
```

#### Attaching files

```php
use Vajexal\AmpMailer\Attachment;
use Vajexal\AmpMailer\Mail;

$mail = (new Mail)
    ->attach(yield Attachment::fromPath('doc.pdf'));
```

#### Embedding files

```php
use Vajexal\AmpMailer\Attachment;
use Vajexal\AmpMailer\Mail;

$mail = new Mail;

$mail->html('<img src="' . $mail->embed(yield Attachment::fromPath('image.png')) . '" alt="Embedding example">');
```

#### Sending few emails

```php
use Vajexal\AmpMailer\Mail;

$mails = \array_map(
    fn ($name) => (new Mail)
        ->from('from@example.com')
        ->to(\sprintf('%s@example.com', \mb_strtolower($name)))
        ->subject('Hello')
        ->text(\sprintf('Hello %s', $name)),
    ['Bar', 'Baz']
);

yield $mailer->sendMany($mails);
```
