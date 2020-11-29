<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Mail;

class CcHeader extends AddressListHeader
{
    protected function getHeaderName(): string
    {
        return 'Cc';
    }

    protected function getAddressList(Mail $mail): array
    {
        return $mail->getCc();
    }
}
