<?php

namespace Vajexal\AmpMailer\Smtp\Exception;

use Vajexal\AmpMailer\Exception\Exception;

class SmtpException extends Exception
{
    public static function tlsRequired(): self
    {
        return new static('TLS is required but server does not support it');
    }

    public static function noAuthStrategy(): self
    {
        return new static('Could not find auth strategy');
    }
}
