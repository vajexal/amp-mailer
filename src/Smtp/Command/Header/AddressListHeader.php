<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Address;
use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\AddressFormatter;
use const Vajexal\AmpMailer\Smtp\SMTP_LINE_BREAK;

abstract class AddressListHeader implements Header
{
    private AddressFormatter $addressFormatter;

    public function __construct(AddressFormatter $addressFormatter)
    {
        $this->addressFormatter = $addressFormatter;
    }

    abstract protected function getHeaderName(): string;

    abstract protected function getAddressList(Mail $mail): array;

    public function get(Mail $mail): string
    {
        $addressList = \array_map(fn (Address $address) => $this->addressFormatter->format($address), $this->getAddressList($mail));

        return \sprintf('%s: %s', $this->getHeaderName(), \implode(\sprintf(',%s ', SMTP_LINE_BREAK), $addressList));
    }
}
