<?php

namespace Vajexal\AmpMailer\Exception;

class AttachmentException extends Exception
{
    public static function invalidDisposition(string $disposition): self
    {
        return new static(\sprintf('Invalid disposition %s', $disposition));
    }
}
