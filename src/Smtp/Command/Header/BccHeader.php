<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Mail;

class BccHeader extends AddressListHeader
{
    protected function getHeaderName(): string
    {
        return 'Bcc';
    }

    protected function getAddressList(Mail $mail): array
    {
        return $mail->getBcc();
    }
}
