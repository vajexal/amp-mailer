<?php

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Exception\MailException;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\AddressFormatter;

class FromHeader implements Header
{
    private AddressFormatter $addressFormatter;

    public function __construct(AddressFormatter $addressFormatter)
    {
        $this->addressFormatter = $addressFormatter;
    }

    public function get(Mail $mail): string
    {
        if (!$mail->getFrom()) {
            throw MailException::emptyFrom();
        }

        return 'From: ' . $this->addressFormatter->format($mail->getFrom());
    }
}
