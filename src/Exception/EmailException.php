<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Exception;

class EmailException extends Exception
{
    const INVALID_EMAIL_CODE      = 0;
    const INVALID_LOCAL_PART_CODE = 1;
    const INVALID_DOMAIN_CODE     = 2;

    public static function invalidEmail(string $email): self
    {
        return new static(\sprintf('Email %s is invalid', $email), self::INVALID_EMAIL_CODE);
    }

    public static function invalidLocalPart(string $email): self
    {
        return new static(\sprintf('Non-ASCII characters are not supported in local part of %s', $email), self::INVALID_LOCAL_PART_CODE);
    }

    public static function invalidDomainPart(string $email): self
    {
        return new static(\sprintf('Invalid domain part of %s', $email), self::INVALID_DOMAIN_CODE);
    }
}
