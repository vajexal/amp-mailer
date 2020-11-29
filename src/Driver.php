<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer;

use Amp\Promise;

interface Driver
{
    public function send(Mail $mail): Promise;
}
