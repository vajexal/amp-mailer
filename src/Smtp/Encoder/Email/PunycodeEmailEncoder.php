<?php

namespace Vajexal\AmpMailer\Smtp\Encoder\Email;

use Vajexal\AmpMailer\Exception\EmailException;

class PunycodeEmailEncoder implements EmailEncoder
{
    public function encode(string $email): string
    {
        [$local, $domain] = \explode('@', $email);

        if (\preg_match('/[^\x00-\x7F]/', $local)) {
            throw EmailException::invalidLocalPart($email);
        }

        $domain = \idn_to_ascii($domain);

        if (!$domain) {
            throw EmailException::invalidDomainPart($email);
        }

        return \implode('@', [$local, $domain]);
    }
}
