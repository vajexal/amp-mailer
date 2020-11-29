<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp;

use SplPriorityQueue;
use Vajexal\AmpMailer\Smtp\Command\Auth\AuthStrategy;

class SmtpServer
{
    private ConnectionConfig $connectionConfig;
    private bool             $supportsAuth = false;
    /** @var SplPriorityQueue<AuthStrategy> */
    private SplPriorityQueue $authStrategies;
    private bool             $supportsTls = false;
    private ?int             $size;

    public function __construct(ConnectionConfig $connectionConfig)
    {
        $this->connectionConfig = $connectionConfig;
        $this->authStrategies   = new SplPriorityQueue;
    }

    public function getConnectionConfig(): ConnectionConfig
    {
        return $this->connectionConfig;
    }

    public function supportsAuth(): bool
    {
        return $this->supportsAuth;
    }

    public function setSupportsAuth(bool $supportsAuth = true): self
    {
        $this->supportsAuth = $supportsAuth;

        return $this;
    }

    public function getAuthStrategies(): SplPriorityQueue
    {
        return $this->authStrategies;
    }

    public function addAuthStrategy(AuthStrategy $strategy): self
    {
        $this->authStrategies->insert($strategy, $strategy->getPriority());

        return $this;
    }

    public function supportsTls(): bool
    {
        return $this->supportsTls;
    }

    public function setSupportsTls(bool $supportsTls = true): self
    {
        $this->supportsTls = $supportsTls;

        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }
}
