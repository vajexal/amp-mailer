<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests;

use Amp\Promise;
use Amp\Success;
use Vajexal\AmpMailer\Driver;

class EmptyDriver implements Driver
{
    public function send(array $mails): Promise
    {
        return new Success;
    }
}
