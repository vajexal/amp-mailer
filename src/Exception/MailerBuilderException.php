<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Exception;

class MailerBuilderException extends Exception
{
    const MISSING_REQUIRED_FIELDS_CODE = 0;

    public static function missingRequiredFields(array $fields): self
    {
        return new static(\sprintf('Missing fields %s', \implode(', ', $fields)), self::MISSING_REQUIRED_FIELDS_CODE);
    }
}
