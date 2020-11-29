<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Mail;

class FromHeader extends AddressListHeader
{
    protected function getHeaderName(): string
    {
        return 'From';
    }

    protected function getAddressList(Mail $mail): array
    {
        return [$mail->getFrom()];
    }
}
