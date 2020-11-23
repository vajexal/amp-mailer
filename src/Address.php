<?php

namespace Vajexal\AmpMailer;

class Address
{
    private string  $email;
    private ?string $name;

    public function __construct(string $email, ?string $name = null)
    {
        $this->email = $email;
        $this->name  = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
