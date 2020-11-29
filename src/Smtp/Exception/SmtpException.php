<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Exception;

use Vajexal\AmpMailer\Exception\Exception;

class SmtpException extends Exception
{
    const TLS_REQUIRED_CODE     = 0;
    const NO_AUTH_STRATEGY_CODE = 0;

    public static function tlsRequired(): self
    {
        return new static('TLS is required but server does not support it', self::TLS_REQUIRED_CODE);
    }

    public static function noAuthStrategy(): self
    {
        return new static('Could not find auth strategy', self::NO_AUTH_STRATEGY_CODE);
    }
}
