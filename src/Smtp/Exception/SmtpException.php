<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Exception;

use Amp\Socket\SocketException;
use Vajexal\AmpMailer\Exception\Exception;

class SmtpException extends Exception
{
    const TLS_REQUIRED_CODE        = 0;
    const NO_AUTH_STRATEGY_CODE    = 1;
    const UNABLE_TO_SETUP_TLS      = 2;
    const UNEXPECTED_RESPONSE_CODE = 3;

    public static function tlsRequired(): self
    {
        return new static('TLS is required but server does not support it', self::TLS_REQUIRED_CODE);
    }

    public static function noAuthStrategy(): self
    {
        return new static('Could not find auth strategy', self::NO_AUTH_STRATEGY_CODE);
    }

    public static function unableToSetupTls(SocketException $e): self
    {
        return new static('Unable to setup tls', self::UNABLE_TO_SETUP_TLS, $e);
    }

    public static function unexpectedResponse(string $response): self
    {
        return new static(\sprintf('Unexpected response: %s', $response), self::UNEXPECTED_RESPONSE_CODE);
    }
}
