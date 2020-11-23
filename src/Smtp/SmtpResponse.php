<?php

namespace Vajexal\AmpMailer\Smtp;

class SmtpResponse
{
    private int $code;
    private string $content;

    public function __construct(int $code, string $content = '')
    {
        $this->code    = $code;
        $this->content = $content;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
