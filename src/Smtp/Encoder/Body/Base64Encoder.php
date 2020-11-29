<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Encoder\Body;

use const Vajexal\AmpMailer\Smtp\SMTP_LINE_BREAK;
use const Vajexal\AmpMailer\Smtp\SMTP_MIME_MAX_LINE_LENGTH;

class Base64Encoder implements BodyEncoder
{
    public function encode(string $text): string
    {
        return \rtrim(\chunk_split(\base64_encode($text), SMTP_MIME_MAX_LINE_LENGTH, SMTP_LINE_BREAK));
    }

    public function getName(): string
    {
        return 'base64';
    }
}
