<?php

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Mail;

interface Header
{
    public function get(Mail $mail): string;
}
