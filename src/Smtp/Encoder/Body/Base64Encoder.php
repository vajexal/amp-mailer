<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Encoder\Body;

use Vajexal\AmpMailer\Smtp\SmtpDriver;

class Base64Encoder implements BodyEncoder
{
    public function encode(string $text): string
    {
        return \rtrim(\chunk_split(\base64_encode($text), SmtpDriver::MIME_MAX_LINE_LENGTH, SmtpDriver::LB));
    }

    public function getName(): string
    {
        return 'base64';
    }
}
