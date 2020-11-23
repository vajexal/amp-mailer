<?php

namespace Vajexal\AmpMailer\Smtp\Command\Extension;

use Vajexal\AmpMailer\Smtp\SmtpServer;

class SizeExtension implements Extension
{
    public function check(string $response, SmtpServer $server): void
    {
        if (!\preg_match('/^250[- ]SIZE (\d+)\s?$/mi', $response, $matches)) {
            return;
        }

        $server->setSize((int) $matches[1]);
    }
}
