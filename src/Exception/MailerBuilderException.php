<?php

namespace Vajexal\AmpMailer\Exception;

class MailerBuilderException extends Exception
{
    public static function missingRequiredFields(array $fields): self
    {
        return new static(sprintf('Missing fields %s', implode(', ', $fields)));
    }
}
