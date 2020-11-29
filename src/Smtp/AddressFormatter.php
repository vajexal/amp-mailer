<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp;

use Vajexal\AmpMailer\Address;
use Vajexal\AmpMailer\Smtp\Encoder\Email\EmailEncoder;
use Vajexal\AmpMailer\Smtp\Encoder\Header\HeaderEncoder;

class AddressFormatter
{
    private EmailEncoder  $emailEncoder;
    private HeaderEncoder $headerEncoder;

    public function __construct(EmailEncoder $emailEncoder, HeaderEncoder $headerEncoder)
    {
        $this->emailEncoder  = $emailEncoder;
        $this->headerEncoder = $headerEncoder;
    }

    public function format(Address $address): string
    {
        if ($address->getName()) {
            $name  = $this->headerEncoder->encode($address->getName());
            $email = $this->emailEncoder->encode($address->getEmail());

            $nameLines = \explode(SMTP_LINE_BREAK, $name);
            $lastLine  = \sprintf('%s <%s>', \end($nameLines), $email);

            if (\strlen($lastLine) > SMTP_MIME_MAX_LINE_LENGTH) {
                $name .= SMTP_LINE_BREAK;
            }

            return \sprintf('%s <%s>', $name, $email);
        }

        return $this->emailEncoder->encode($address->getEmail());
    }
}
