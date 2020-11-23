<?php

namespace Vajexal\AmpMailer\Smtp\Encoder\Body;

interface BodyEncoder
{
    public function encode(string $text): string;
    public function getName(): string;
}
