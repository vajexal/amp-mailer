<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Mail;

class MimeVersionHeader implements Header
{
    public function get(Mail $mail): string
    {
        return 'MIME-Version: 1.0';
    }
}
