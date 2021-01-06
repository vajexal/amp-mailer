<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests\Mailtrap;

class Inbox
{
    private int    $id;
    private string $host;
    private int    $port;
    private string $username;
    private string $password;

    public function __construct(array $data)
    {
        $this->id       = $data['id'];
        $this->host     = $data['domain'];
        $this->port     = \end($data['smtp_ports']);
        $this->username = $data['username'];
        $this->password = $data['password'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
