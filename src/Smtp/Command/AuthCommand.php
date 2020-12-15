<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command;

use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\Command\Auth\AuthStrategy;
use Vajexal\AmpMailer\Smtp\Exception\SmtpException;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket\SmtpSocket;
use function Amp\call;

class AuthCommand implements Command
{
    public const COMMAND = 'AUTH';

    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        $strategies = $server->getAuthStrategies();

        if (!$strategies->valid()) {
            throw SmtpException::noAuthStrategy();
        }

        /** @var AuthStrategy $strategy */
        $strategy = $strategies->top();

        yield call([$strategy, 'execute'], $socket, $server, $mail);
    }
}
