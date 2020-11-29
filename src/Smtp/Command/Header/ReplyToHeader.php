<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Mail;

class ReplyToHeader extends AddressListHeader
{
    protected function getHeaderName(): string
    {
        return 'Reply-To';
    }

    protected function getAddressList(Mail $mail): array
    {
        return $mail->getReplyTo();
    }
}
