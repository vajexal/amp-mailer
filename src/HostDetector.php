<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer;

class HostDetector
{
    public function getHost(): string
    {
        if (isset($_SERVER['SERVER_NAME'])) {
            return $_SERVER['SERVER_NAME'];
        }

        if ($host = \gethostname()) {
            return $host;
        }

        return 'localhost';
    }
}
