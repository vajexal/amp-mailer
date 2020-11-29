<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Smtp\Command\Auth;

use Vajexal\AmpMailer\Mail;
use Vajexal\AmpMailer\Smtp\SmtpResponse;
use Vajexal\AmpMailer\Smtp\SmtpServer;
use Vajexal\AmpMailer\Smtp\SmtpSocket;

class CramMd5AuthStrategy implements AuthStrategy
{
    public function getPriority(): int
    {
        return 3;
    }

    public function execute(SmtpSocket $socket, SmtpServer $server, Mail $mail)
    {
        /** @var SmtpResponse $response */
        $response = yield $socket->send('AUTH CRAM-MD5', [334]);

        $challenge        = \base64_decode($response->getContent());
        $connectionConfig = $server->getConnectionConfig();

        $answer = \base64_encode($connectionConfig->getUsername() . ' ' . \hash_hmac('md5', $challenge, $connectionConfig->getPassword()));

        yield $socket->send($answer, [235]);
    }
}
