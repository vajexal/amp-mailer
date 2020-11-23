<?php

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Address;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\AddressFormatter;
use Vajexal\AmpMailer\Smtp\SmtpDriver;

class ToHeader implements Header
{
    private AddressFormatter $addressFormatter;

    public function __construct(AddressFormatter $addressFormatter)
    {
        $this->addressFormatter = $addressFormatter;
    }

    public function get(Mail $mail): string
    {
        $addressList = \array_map(fn (Address $address) => $this->addressFormatter->format($address), $mail->getTo());

        return 'To: ' . \implode(\sprintf(',%s ', SmtpDriver::LB), $addressList);
    }
}
