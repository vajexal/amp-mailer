<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Extension;

use Vajexal\AmpMailer\Smtp\SmtpServer;

class PipeliningExtension implements Extension
{
    public function check(string $response, SmtpServer $server): void
    {
        if (\preg_match('/^250[- ]PIPELINING\s?$/mi', $response)) {
            $server->setSupportsPipelining();
        }
    }
}
