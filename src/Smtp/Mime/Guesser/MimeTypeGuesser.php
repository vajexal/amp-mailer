<?php

namespace Vajexal\AmpMailer\Smtp\Mime\Guesser;

use Amp\Promise;

interface MimeTypeGuesser
{
    public function guess(string $path): Promise;
}
