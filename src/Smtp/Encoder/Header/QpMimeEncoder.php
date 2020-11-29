<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Encoder\Header;

use Vajexal\AmpMailer\Smtp\SmtpDriver;

class QpMimeEncoder implements HeaderEncoder
{
    public function encode(string $text): string
    {
        if (\preg_match('/^[\x00-\x7F]*$/', $text)) { // if it's ascii text
            return $text;
        }

        $text = \iconv_mime_encode('', $text, [
            'scheme'           => 'B',
            'line-length'      => SmtpDriver::MIME_MAX_LINE_LENGTH,
            'line-break-chars' => SmtpDriver::LB,
        ]);

        return \substr($text, 2);
    }
}
