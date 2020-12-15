<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp;

class SmtpRequest
{
    private string $message;
    private array  $expectedCodes;

    public function __construct(string $message, array $expectedCodes)
    {
        $this->message       = $message;
        $this->expectedCodes = $expectedCodes;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getExpectedCodes(): array
    {
        return $this->expectedCodes;
    }
}
