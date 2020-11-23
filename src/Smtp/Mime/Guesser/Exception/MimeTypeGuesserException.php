<?php

namespace Vajexal\AmpMailer\Smtp\Mime\Guesser\Exception;

use Vajexal\AmpMailer\Exception\Exception;

class MimeTypeGuesserException extends Exception
{
    public static function invalidPath(string $path): self
    {
        return new static(\sprintf('%s is not a file', $path));
    }

    public static function couldNotDetect(string $path): self
    {
        return new static(\sprintf('Could not detect mime type of %s', $path));
    }
}
