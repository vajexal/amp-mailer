<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Encoder\Header;

use const Vajexal\AmpMailer\Smtp\QP_MIME_BASE64_SCHEME;
use const Vajexal\AmpMailer\Smtp\SMTP_LINE_BREAK;
use const Vajexal\AmpMailer\Smtp\SMTP_MIME_MAX_LINE_LENGTH;

class QpMimeEncoder implements HeaderEncoder
{
    public function encode(string $text): string
    {
        $text = \str_replace(["\r", "\n"], '', $text);

        if (\preg_match('/^[\w ]*$/', $text)) {
            return $text;
        }

        $text = \iconv_mime_encode('', $text, [
            'scheme'           => QP_MIME_BASE64_SCHEME,
            'line-length'      => SMTP_MIME_MAX_LINE_LENGTH,
            'line-break-chars' => SMTP_LINE_BREAK,
        ]);

        return \substr($text, \strlen(': '));
    }
}
