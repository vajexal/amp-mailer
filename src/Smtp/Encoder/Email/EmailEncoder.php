<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Encoder\Email;

interface EmailEncoder
{
    public function encode(string $email): string;
}
