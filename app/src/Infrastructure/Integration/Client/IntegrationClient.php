<?php

declare(strict_types=1);

namespace Infrastructure\Integration\Client;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class IntegrationClient
{
    private Client $client;

    public function __construct(?Client $client = null, array $config = [])
    {
        $config['handler'] = $this->getLogMiddleware();
        $config['curl.options'] = [
            'CURLOPT_SSLVERSION' => '6',
        ];
        $this->client = $client === null ? new Client($config) : $client;
    }

    public static function make(?Client $client = null, array $config = []): self
    {
        return new self($client, $config);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    private function getLogMiddleware(): HandlerStack
    {
        $handlerStack = HandlerStack::create();
        $handlerStack->push(
            function (callable $handler) {
                return function (RequestInterface $request, array $options) use ($handler) {
                    $promise = $handler($request, $options);

                    return $promise->then(
                        function (ResponseInterface $response) use ($request, $options, $handler) {
                            return $response;
                        }
                    );
                };
            }
        );

        return $handlerStack;
    }
}
