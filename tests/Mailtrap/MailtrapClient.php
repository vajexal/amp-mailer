<?php

declare(strict_types=1);

namespace Vajexal\AmpMailer\Tests\Mailtrap;

use Amp\Http\Client\HttpClient;
use Amp\Http\Client\Request;
use Amp\Http\Client\Response;
use Amp\Promise;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use function Amp\call;

class MailtrapClient
{
    private HttpClient      $httpClient;
    private string          $apiToken;
    private LoggerInterface $logger;

    public function __construct(HttpClient $httpClient, string $apiToken, LoggerInterface $logger = null)
    {
        $this->httpClient = $httpClient;
        $this->apiToken   = $apiToken;
        $this->logger     = $logger ?: new NullLogger;
    }

    private function request(string $endpoint, string $method = 'GET', string $body = null): Promise
    {
        return call(function () use ($endpoint, $method, $body) {
            $request = new Request(\sprintf('https://mailtrap.io%s', $endpoint), $method, $body);
            $request->setHeader('Accept', 'application/json');
            $request->setHeader('Authorization', \sprintf('Token token=%s', $this->apiToken));

            $this->logger->debug(\sprintf('request: %s %s', $request->getMethod(), (string) $request->getUri()));

            /** @var Response $response */
            $response     = yield $this->httpClient->request($request);
            $responseBody = yield $response->getBody()->buffer();

            $this->logger->debug(\sprintf('response: %d %s', $response->getStatus(), $responseBody));

            if (\mb_strpos($response->getHeader('Content-Type'), 'application/json') === 0) {
                return \json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR);
            }

            return $responseBody;
        });
    }

    public function getInbox(int $id): Promise
    {
        return call(function () use ($id) {
            $inbox = yield $this->request(\sprintf('/api/v1/inboxes/%d', $id));

            return new Inbox($inbox);
        });
    }

    public function clearInbox(Inbox $inbox): Promise
    {
        return $this->request(\sprintf('/api/v1/inboxes/%d/clean', $inbox->getId()), 'PATCH');
    }

    public function getMessages(Inbox $inbox): Promise
    {
        return call(function () use ($inbox) {
            $messages = yield $this->request(\sprintf('/api/v1/inboxes/%d/messages', $inbox->getId()));

            return \array_map(fn ($message) => new Message($message), $messages);
        });
    }

    public function getMessageText(Message $message): Promise
    {
        return $this->request(\sprintf('/api/v1/inboxes/%d/messages/%d/body.txt', $message->getInboxId(), $message->getId()));
    }
}
