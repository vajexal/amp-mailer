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
        ->from('foo@example.com')
        ->to('bar@example.com')
        ->subject('Test')
        ->text('Test');

    yield $mailer->send($mail);
});
```

#### More complex mail

```php
use Vajexal\AmpMailer\Mail;

$mail = (new Mail)
    ->from('foo@example.com', 'foo') // email and username
    ->replyTo('foo2@example.com', 'foo2')
    ->replyTo('foo3@example.com')
    ->to('bar1@example.com', 'bar1')
    ->to('bar2@example.com')
    ->cc('baz1@example.com', 'baz1')
    ->cc('baz2@example.com')
    ->bcc('qux1@example.com', 'qux1')
    ->bcc('qux2@example.com')
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
