<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Exception;

class AttachmentException extends Exception
{
    const INVALID_DISPOSITION_CODE = 0;

    public static function invalidDisposition(string $disposition): self
    {
        return new static(\sprintf('Invalid disposition %s', $disposition), self::INVALID_DISPOSITION_CODE);
    }
}
