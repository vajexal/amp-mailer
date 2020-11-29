<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Mime\Node;

interface Node
{
    public function getBody(): string;
}
