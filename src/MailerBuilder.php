<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer;

use Psr\Log\LoggerInterface;
use Vajexal\AmpMailer\Exception\MailerBuilderException;
use Vajexal\AmpMailer\Smtp\ConnectionConfig;
use Vajexal\AmpMailer\Smtp\SmtpDriver;

class MailerBuilder
{
    private string           $host     = '';
    private int              $port     = 0;
    private ?string          $username = null;
    private ?string          $password = null;
    private ?LoggerInterface $logger   = null;

    public function host(string $host): self
    {
        $this->host = $host;

        return $this;
    }

    public function port(int $port): self
    {
        $this->port = $port;

        return $this;
    }

    public function username(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function password(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function logger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }

    public function build(): Mailer
    {
        if (!$this->host || !$this->port) {
            throw MailerBuilderException::missingRequiredFields(['host', 'port']);
        }

        $connectionConfig = new ConnectionConfig($this->host, $this->port, $this->username, $this->password);

        $driver = new SmtpDriver($connectionConfig, $this->logger);

        return new Mailer($driver);
    }
}
