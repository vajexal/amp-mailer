<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Encoder\Body;

interface BodyEncoder
{
    public function encode(string $text): string;
    public function getName(): string;
}
