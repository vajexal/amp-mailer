<?php

namespace Vajexal\AmpMailer\Tests;

use Amp\Socket\ResourceSocket;
use Amp\Socket\Server;
use Amp\Socket\SocketAddress;
use Vajexal\AmpMailer\Smtp\SmtpDriver;
use function Amp\call;
use function Amp\Promise\rethrow;

class DumpSmtpServer
{
    private const RESPONSES = [
        'EHLO' => 250,
        'MAIL' => 250,
        'RCPT' => 250,
        'DATA' => 354,
        'QUIT' => 221,
    ];

    private Server $server;
    private string $content = '';

    public function start(): SocketAddress
    {
        $this->server = Server::listen('127.0.0.1:0');

        rethrow(call(function () {
            /** @var ResourceSocket $socket */
            $socket = yield $this->server->accept();
            $socket->unreference();
            yield $socket->write('220 localhost' . SmtpDriver::LB);

            while (($chunk = yield $socket->read()) !== null) {
                $this->content .= $chunk;

                $command = \substr($chunk, 0, 4);

                if (empty(self::RESPONSES[$command])) {
                    continue;
                }

                yield $socket->write(self::RESPONSES[$command] . ' OK' . SmtpDriver::LB);

                if ($command === 'QUIT') {
                    $socket->close();
                    break;
                }

                if ($command === 'DATA') {
                    $this->content .= yield $socket->read();
                    yield $socket->write('250 OK' . SmtpDriver::LB);
                }
            }
        }));

        return $this->server->getAddress();
    }

    public function stop(): void
    {
        $this->server->close();
    }

    public function getContent(): string
    {
        return $this->content;
    }
}