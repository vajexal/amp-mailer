<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Header;

use Vajexal\AmpMailer\HostDetector;
use Vajexal\AmpMailer\Mail;

class MessageIdHeader implements Header
{
    private HostDetector $hostDetector;

    public function __construct(HostDetector $hostDetector)
    {
        $this->hostDetector = $hostDetector;
    }

    public function get(Mail $mail): string
    {
        return \sprintf('Message-ID: <%s@%s>', \uniqid((string) \mt_rand(), true), $this->hostDetector->getHost());
    }
}
