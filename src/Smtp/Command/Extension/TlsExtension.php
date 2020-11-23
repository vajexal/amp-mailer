<?php

namespace Vajexal\AmpMailer\Smtp\Command\Extension;

use Vajexal\AmpMailer\Smtp\SmtpServer;

class TlsExtension implements Extension
{
    public function check(string $response, SmtpServer $server): void
    {
        if (\preg_match('/^250[- ]STARTTLS\s?$/mi', $response)) {
            $server->setSupportsTls();
        }
    }
}
