<?php

namespace Vajexal\AmpMailer\Exception;

class EmailException extends Exception
{
    public static function invalidEmail(string $email): self
    {
        return new static(\sprintf('Email %s is invalid', $email));
    }

    public static function invalidLocalPart(string $email): self
    {
        return new static(\sprintf('Non-ASCII characters are not supported in local part of %s', $email));
    }

    public static function invalidDomainPart(string $email): self
    {
        return new static(\sprintf('Invalid domain part of %s', $email));
    }
}
