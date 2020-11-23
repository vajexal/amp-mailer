<?php

namespace Vajexal\AmpMailer\Smtp\Command\Extension;

use Vajexal\AmpMailer\DiLocator;
use Vajexal\AmpMailer\Smtp\SmtpServer;

class AuthExtension implements Extension
{
    public function check(string $response, SmtpServer $server): void
    {
        if (!\preg_match('/^250[- ]AUTH ([\w\-\s]+)$/mi', $response, $matches)) {
            return;
        }

        $server->setSupportsAuth();

        $authTypes = \explode(' ', \trim($matches[1]));

        if (\in_array('CRAM-MD5', $authTypes, true)) {
            $server->addAuthStrategy(DiLocator::cramMd5AuthStrategy());
        }

        if (\in_array('LOGIN', $authTypes, true)) {
            $server->addAuthStrategy(DiLocator::loginAuthStrategy());
        }

        if (\in_array('PLAIN', $authTypes, true)) {
            $server->addAuthStrategy(DiLocator::plainAuthStrategy());
        }
    }
}
