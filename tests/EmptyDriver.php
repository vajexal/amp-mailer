<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests;

use Amp\Promise;
use Amp\Success;
use Vajexal\AmpMailer\Driver;
use Vajexal\AmpMailer\Mail;

class EmptyDriver implements Driver
{
    public function send(Mail $mail): Promise
    {
        return new Success;
    }
}
