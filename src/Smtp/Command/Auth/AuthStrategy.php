<?php

namespace Vajexal\AmpMailer\Smtp\Command\Auth;

use Vajexal\AmpMailer\Smtp\Command\Command;

interface AuthStrategy extends Command
{
    public function getPriority(): int;
}
