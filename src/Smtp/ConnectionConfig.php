<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp;

class ConnectionConfig
{
    private string  $host;
    private int     $port;
    private ?string $username;
    private ?string $password;

    public function __construct(string $host, int $port, ?string $username = null, ?string $password = null)
    {
        $this->host     = $host;
        $this->port     = $port;
        $this->username = $username;
        $this->password = $password;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function hasCredentials(): bool
    {
        return $this->username && $this->password;
    }
}
