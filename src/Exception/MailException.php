<?php

namespace Vajexal\AmpMailer\Exception;

class MailException extends Exception
{
    public static function emptyRecipients(): self
    {
        return new static('Empty recipients');
    }

    public static function emptyFrom(): self
    {
        return new static('Empty from');
    }

    public static function emptyMail(): self
    {
        return new static('Mail has no content');
    }
}
