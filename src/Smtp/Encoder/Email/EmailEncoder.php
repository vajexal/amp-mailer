<?php

namespace Vajexal\AmpMailer\Smtp\Encoder\Email;

interface EmailEncoder
{
    public function encode(string $email): string;
}
