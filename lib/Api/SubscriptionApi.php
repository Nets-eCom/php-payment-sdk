<?php

declare(strict_types=1);

namespace NexiCheckout\Api;

use NexiCheckout\Api\Exception\ClientErrorPaymentApiException;
use NexiCheckout\Api\Exception\InternalErrorPaymentApiException;
use NexiCheckout\Api\Exception\PaymentApiException;
use NexiCheckout\Http\Header\IdempotencyKey;
use NexiCheckout\Http\HttpClient;
use NexiCheckout\Http\HttpClientException;
use NexiCheckout\Http\RequestHeaderOptions;
use NexiCheckout\Model\Request\BulkChargeSubscription;
use NexiCheckout\Model\Request\ChargeSubscription;
use NexiCheckout\Model\Request\VerifySubscriptions;
use NexiCheckout\Model\Result\BulkChargeSubscriptionResult;
use NexiCheckout\Model\Result\RetrieveBulkVerificationsResult;
use NexiCheckout\Model\Result\RetrieveSubscriptionBulkChargesResult;
use NexiCheckout\Model\Result\RetrieveSubscriptionResult;
use NexiCheckout\Model\Result\SubscriptionCharges\SingleSubscriptionCharge;
use NexiCheckout\Model\Result\VerifySubscriptionsResult;

class SubscriptionApi
{
    private const SUBSCRIPTIONS_ENDPOINT = '/v1/subscriptions';

    private const SUBSCRIPTION_CHARGES_BULK = self::SUBSCRIPTIONS_ENDPOINT . '/charges';

    private const SUBSCRIPTION_VERIFICATIONS = self::SUBSCRIPTIONS_ENDPOINT . '/verifications';

    private const SUBSCRIPTION_CHARGES = self::SUBSCRIPTIONS_ENDPOINT . '/%s/charges';

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

    public function bulkChargeSubscription(BulkChargeSubscription $bulkChargeSubscription): BulkChargeSubscriptionResult
    {
        try {
            $response = $this->client->post(
                self::SUBSCRIPTION_CHARGES_BULK,
                json_encode($bulkChargeSubscription)
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                'Couldn\'t bulk charge subscription',
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return BulkChargeSubscriptionResult::fromJson($contents);
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
    public function verifySubscriptions(VerifySubscriptions $verifySubscriptions): VerifySubscriptionsResult
    {
        try {
            $response = $this->client->post(self::SUBSCRIPTION_VERIFICATIONS, json_encode($verifySubscriptions));
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                'Couldn\'t verify subscriptions',
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return VerifySubscriptionsResult::fromJson($contents);
    }

    public function chargeSubscription(string $subscriptionId, ChargeSubscription $chargeSubscription, ?string $idempotencyKey = null): SingleSubscriptionCharge
    {
        try {
            $response = $this->client->post(
                \sprintf(
                    self::SUBSCRIPTION_CHARGES,
                    $subscriptionId
                ),
                json_encode($chargeSubscription),
                $this->idempotencyOptions($idempotencyKey)
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t retrieve charge for subscription with ID: %s', $subscriptionId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return SingleSubscriptionCharge::fromJson($contents);

    }

    /**
     * @throws PaymentApiException
     * @throws \JsonException
     */
    public function retrieveBulkVerifications(string $bulkId): RetrieveBulkVerificationsResult
    {
        try {
            $response = $this->client->get(
                \sprintf(
                    '%s/%s',
                    self::SUBSCRIPTION_VERIFICATIONS,
                    $bulkId
                )
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t retrieve verifications for bulk ID: %s', $bulkId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return RetrieveBulkVerificationsResult::fromJson($contents);
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

    private function idempotencyOptions(?string $key): ?RequestHeaderOptions
    {
        if ($key === null) {
            return null;
        }

        return RequestHeaderOptions::create()->with(new IdempotencyKey($key));
    }
}
