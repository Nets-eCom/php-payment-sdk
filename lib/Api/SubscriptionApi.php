<?php

declare(strict_types=1);

namespace NexiCheckout\Api;

use NexiCheckout\Api\Exception\ClientErrorPaymentApiException;
use NexiCheckout\Api\Exception\InternalErrorPaymentApiException;
use NexiCheckout\Api\Exception\PaymentApiException;
use NexiCheckout\Http\HttpClient;
use NexiCheckout\Http\HttpClientException;
use NexiCheckout\Model\Result\RetrieveSubscriptionBulkChargesResult;
use NexiCheckout\Model\Result\RetrieveSubscriptionResult;

class SubscriptionApi
{
    private const SUBSCRIPTIONS_ENDPOINT = '/v1/subscriptions';

    private const SUBSCRIPTION_CHARGES_BULK = self::SUBSCRIPTIONS_ENDPOINT . '/charges';

    public function __construct(
        private readonly HttpClient $client,
    ) {
    }

    /**
     * @throws PaymentApiException
     * @throws \JsonException
     */
    public function retrieveSubscription(string $subscriptionId): RetrieveSubscriptionResult
    {
        return $this->fetchSubscription(
            \sprintf('%s/%s', self::SUBSCRIPTIONS_ENDPOINT, $subscriptionId),
            $subscriptionId
        );
    }

    /**
     * @throws PaymentApiException
     * @throws \JsonException
     */
    public function retrieveSubscriptionByExternalReference(
        string $subscriptionId,
        string $externalReference
    ): RetrieveSubscriptionResult {
        return $this->fetchSubscription(
            \sprintf('%s/%s?externalReference=%s', self::SUBSCRIPTIONS_ENDPOINT, $subscriptionId, $externalReference),
            $subscriptionId
        );
    }

    /**
     * @throws PaymentApiException
     * @throws \JsonException
     */
    public function retrieveSubscriptionBulkCharges(string $bulkId): RetrieveSubscriptionBulkChargesResult
    {
        try {
            $response = $this->client->get(
                \sprintf(
                    '%s/%s',
                    self::SUBSCRIPTION_CHARGES_BULK,
                    $bulkId
                )
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t retrieve subscription charges for bulk ID: %s', $bulkId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return RetrieveSubscriptionBulkChargesResult::fromJson($contents);
    }

    /**
     * @throws PaymentApiException
     * @throws \JsonException
     */
    private function fetchSubscription(string $url, string $subscriptionId): RetrieveSubscriptionResult
    {
        try {
            $response = $this->client->get($url);
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t retrieve subscription for a given id: %s', $subscriptionId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return RetrieveSubscriptionResult::fromJson($contents);
    }

    private function isSuccessCode(int $code): bool
    {
        return $code >= 200 && $code < 300;
    }

    /**
     * @throws \JsonException
     */
    private function createPaymentApiException(int $code, string $contents): PaymentApiException
    {
        return match (true) {
            $code >= 300 && $code < 400 => new PaymentApiException('Redirection not supported'),
            $code === 400 => new ClientErrorPaymentApiException(\sprintf('Client error: %s', $contents), $contents),
            $code === 401 => new PaymentApiException(\sprintf('Unauthorized: %s', $contents)),
            $code === 404 => new PaymentApiException(\sprintf('Client error: %s', $contents)),
            $code >= 402 && $code < 500 => new InternalErrorPaymentApiException($contents),
            $code >= 500 && $code < 600 => new PaymentApiException(\sprintf('Server error occurred: %s', $contents)),
            default => new PaymentApiException(\sprintf('Unexpected status code: %d', $code)),
        };
    }
}
