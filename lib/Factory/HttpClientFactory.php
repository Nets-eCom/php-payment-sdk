<?php

declare(strict_types=1);

namespace NexiCheckout\Factory;

use NexiCheckout\Http\Configuration;
use NexiCheckout\Http\HttpClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

class HttpClientFactory
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
    ) {
    }

    public function create(Configuration $configuration): HttpClient
    {
        return new HttpClient(
            $this->client,
            $this->requestFactory,
            $this->streamFactory,
            $configuration
        );
    }
}
