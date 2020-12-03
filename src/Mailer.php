<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer;

use Amp\Promise;

class Mailer
{
    private Driver        $driver;
    private MailValidator $validator;

    public function __construct(Driver $driver)
    {
        $this->driver    = $driver;
        $this->validator = DiLocator::mailValidator();
    }

    public function send(Mail $mail): Promise
    {
        return $this->sendMany([$mail]);
    }

    public function sendMany(iterable $mails): Promise
    {
        foreach ($mails as $mail) {
            $this->validator->validate($mail);
        }

        return $this->driver->send($mails);
    }
}
