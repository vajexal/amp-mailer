<?php

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Mail;

class DateHeader implements Header
{
    public function get(Mail $mail): string
    {
        return 'Date: ' . \date('D, d M Y H:i:s O');
    }
}
