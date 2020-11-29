<?php

namespace Vajexal\AmpMailer\Exception;

class MailException extends Exception
{
    const EMPTY_RECIPIENTS_CODE = 0;
    const EMPTY_FROM_CODE       = 1;
    const EMPTY_MAIL_CODE       = 2;

    public static function emptyRecipients(): self
    {
        return new static('Empty recipients', self::EMPTY_RECIPIENTS_CODE);
    }

    public static function emptyFrom(): self
    {
        return new static('Empty from', self::EMPTY_FROM_CODE);
    }

    public static function emptyMail(): self
    {
        return new static('Mail has no content', self::EMPTY_MAIL_CODE);
    }
}
