<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Extension;

use Vajexal\AmpMailer\Smtp\SmtpServer;

class SizeExtension implements Extension
{
    public function check(string $response, SmtpServer $server): void
    {
        if (!\preg_match('/^250[- ]SIZE ?(\d+)?\s?$/mi', $response, $matches)) {
            return;
        }

        // https://tools.ietf.org/html/rfc1870#section-7
        // The numeric parameter to the EHLO SIZE keyword is optional
        if (isset($matches[1])) {
            $server->setSize((int) $matches[1]);
        }
    }
}
