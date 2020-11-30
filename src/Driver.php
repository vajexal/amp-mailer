<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer;

use Amp\Promise;

interface Driver
{
    /**
     * @param Mail[] $mails
     * @return Promise<void>
     */
    public function send(array $mails): Promise;
}
