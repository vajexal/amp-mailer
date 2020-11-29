<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Mail;

class ToHeader extends AddressListHeader
{
    protected function getHeaderName(): string
    {
        return 'To';
    }

    protected function getAddressList(Mail $mail): array
    {
        return $mail->getTo();
    }
}
