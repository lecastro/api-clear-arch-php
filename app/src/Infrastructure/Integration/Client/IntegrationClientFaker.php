<?php

declare(strict_types=1);

namespace Infrastructure\Integration\Client;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Handler\MockHandler;

class IntegrationClientFaker
{
    private Client $client;

    /** @var array<string> */
    public function __construct(array $body)
    {
        $mock = new MockHandler([
            new Response(
                200,
                [],
                json_encode($body)
            ),
        ]);

        $handler = HandlerStack::create($mock);

        $this->client = new Client(['handler' => $handler]);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    /** @param array<mixed> $body */
    public static function make(array $body = []): self
    {
        return new self($body);
    }
}
