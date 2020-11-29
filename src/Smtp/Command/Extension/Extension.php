<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Extension;

use Vajexal\AmpMailer\Smtp\SmtpServer;

interface Extension
{
    public function check(string $response, SmtpServer $server): void;
}
