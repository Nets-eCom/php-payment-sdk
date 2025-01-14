<?php

declare(strict_types=1);

namespace NexiCheckout\Http;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;

class HttpClient
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory,
        private readonly Configuration $configuration
    ) {
    }

    /**
     * @throws HttpClientException
     */
    public function get(string $path): ResponseInterface
    {
        return $this->send(
            $this->createRequest(
                $this->createUrl($path),
                'GET'
            )->withHeader('Accept', 'application/json')
        );
    }

    /**
     * @throws HttpClientException
     */
    public function post(string $path, string $body): ResponseInterface
    {
        return $this->send(
            $this->createRequest($this->createUrl($path), 'POST')->withBody($this->streamFactory->createStream($body))
        );
    }

    /**
     * @throws HttpClientException
     */
    public function put(string $path, string $body): ResponseInterface
    {
        return $this->send(
            $this->createRequest($this->createUrl($path), 'PUT')->withBody($this->streamFactory->createStream($body))
        );
    }

    private function createRequest(string $url, string $method): RequestInterface
    {
        $request = $this->requestFactory->createRequest($method, $url);

        $headers = [
            'Authorization' => $this->configuration->getSecretKey(),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $commercePlatformTag = $this->configuration->getCommercePlatformTag();

        if ($commercePlatformTag !== null) {
            $headers['CommercePlatformTag'] = $commercePlatformTag;
        }

        foreach ($headers as $key => $value) {
            $request = $request->withHeader($key, $value);
        }

        return $request;
    }

    private function send(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $clientException) {
            throw new HttpClientException($clientException->getMessage(), $clientException->getCode(), $clientException);
        }
    }

    private function createUrl(string $path): string
    {
        return sprintf('%s/%s', $this->configuration->getBaseUrl(), $path);
    }
}
