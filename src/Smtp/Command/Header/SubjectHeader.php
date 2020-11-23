<?php

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Encoder\Header\HeaderEncoder;

class SubjectHeader implements Header
{
    private HeaderEncoder $encoder;

    public function __construct(HeaderEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    public function get(Mail $mail): string
    {
        $subject = $this->encoder->encode($mail->getSubject());

        return \sprintf('Subject: %s', $subject);
    }
}
