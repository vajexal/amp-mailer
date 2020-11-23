<?php

namespace Vajexal\AmpMailer\Smtp\Encoder\Header;

interface HeaderEncoder
{
    public function encode(string $text): string;
}
