<?php

declare(strict_types=1);

namespace NexiCheckout\Api;

use NexiCheckout\Api\Exception\ClientErrorPaymentApiException;
use NexiCheckout\Api\Exception\InternalErrorPaymentApiException;
use NexiCheckout\Api\Exception\PaymentApiException;
use NexiCheckout\Http\HttpClient;
use NexiCheckout\Http\HttpClientException;
use NexiCheckout\Model\Request\Cancel;
use NexiCheckout\Model\Request\Charge;
use NexiCheckout\Model\Request\MyReference;
use NexiCheckout\Model\Request\Payment;
use NexiCheckout\Model\Request\ReferenceInformation;
use NexiCheckout\Model\Request\RefundCharge;
use NexiCheckout\Model\Request\RefundPayment;
use NexiCheckout\Model\Request\UpdateOrder;
use NexiCheckout\Model\Result\ChargeResult;
use NexiCheckout\Model\Result\Payment\PaymentWithHostedCheckoutResult;
use NexiCheckout\Model\Result\RefundChargeResult;
use NexiCheckout\Model\Result\RefundPaymentResult;
use NexiCheckout\Model\Result\RetrievePaymentResult;

class PaymentApi
{
    private const PAYMENTS_ENDPOINT = '/v1/payments';

    private const PAYMENT_CHARGES = '/charges';

    private const PAYMENT_CANCELS = '/cancels';

    private const PAYMENT_UPDATE_REFERENCE_INFORMATION = '/referenceinformation';

    private const PAYMENT_UPDATE_MY_REFERENCE = '/myreference';

    private const PAYMENT_UPDATE_ORDER = '/orderitems';

    private const PAYMENT_TERMINATE = '/terminate';

    private const CHARGES_ENDPOINT = '/v1/charges';

    private const REFUNDS = '/refunds';

    private const PENDING_REFUNDS_ENDPOINT = '/v1/pending-refunds';

    private const REFUND_CANCELS = '/cancel';

    public function __construct(
        private readonly HttpClient $client,
    ) {
    }

    /**
     * @throws PaymentApiException
     */
    public function createPayment(Payment $payment): PaymentWithHostedCheckoutResult
    {
        try {
            $response = $this->client->post(self::PAYMENTS_ENDPOINT, json_encode($payment));
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                'Couldn\'t create payment',
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return PaymentWithHostedCheckoutResult::fromJson($contents);
    }

    /**
     * @throws PaymentApiException
     */
    public function retrievePayment(string $paymentId): RetrievePaymentResult
    {
        try {
            $response = $this->client->get(\sprintf('%s/%s', self::PAYMENTS_ENDPOINT, $paymentId));
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t retrieve payment for a given id: %s', $paymentId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return RetrievePaymentResult::fromJson($contents);
    }

    /**
     * @throws PaymentApiException
     */
    public function cancel(string $paymentId, Cancel $cancel): void
    {
        try {
            $response = $this->client->post($this->getPaymentOperationPath($paymentId, self::PAYMENT_CANCELS), json_encode($cancel));
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t cancel for a given payment id: %s', $paymentId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }
    }

    public function updateReferenceInformation(string $paymentId, ReferenceInformation $referenceInformation): void
    {
        try {
            $response = $this->client->put(
                $this->getPaymentOperationPath($paymentId, self::PAYMENT_UPDATE_REFERENCE_INFORMATION),
                json_encode($referenceInformation)
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t update reference information for a given payment id: %s', $paymentId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }
    }

    /**
     * @throws PaymentApiException
     * @throws \JsonException
     */
    public function updateMyReference(string $paymentId, MyReference $myReference): void
    {
        try {
            $response = $this->client->put(
                $this->getPaymentOperationPath($paymentId, self::PAYMENT_UPDATE_MY_REFERENCE),
                json_encode($myReference)
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t update myReference information for a given payment id: %s', $paymentId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $response->getBody()->getContents());
        }
    }

    /**
     * @throws PaymentApiException
     * @throws \JsonException
     */
    public function updatePaymentOrder(
        string $paymentId,
        UpdateOrder $updateOrder
    ): void {
        try {
            $response = $this->client->put(
                $this->getPaymentOperationPath($paymentId, self::PAYMENT_UPDATE_ORDER),
                json_encode($updateOrder)
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t update payment order for a given payment id: %s', $paymentId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $response->getBody()->getContents());
        }
    }

    /**
     * @throws PaymentApiException
     */
    public function terminate(string $paymentId): void
    {
        try {
            $response = $this->client->put(
                $this->getPaymentOperationPath($paymentId, self::PAYMENT_TERMINATE),
                ''
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t terminate payment id: %s', $paymentId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $response->getBody()->getContents());
        }
    }

    public function charge(string $paymentId, Charge $charge): ChargeResult
    {
        try {
            $response = $this->client->post($this->getPaymentOperationPath($paymentId, self::PAYMENT_CHARGES), json_encode($charge));
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t create charge for a given payment id: %s', $paymentId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return ChargeResult::fromJson($contents);
    }

    /**
     * @throws PaymentApiException
     */
    public function refundCharge(string $chargeId, RefundCharge $refund): RefundChargeResult
    {
        try {
            $response = $this->client->post(
                \sprintf('%s/%s%s', self::CHARGES_ENDPOINT, $chargeId, self::REFUNDS),
                json_encode($refund)
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t refund charge with id: %s', $chargeId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return RefundChargeResult::fromJson($contents);
    }

    /**
     * @throws PaymentApiException
     * @throws \JsonException
     */
    public function refundPayment(string $paymentId, RefundPayment $refundPayment): RefundPaymentResult
    {
        try {
            $response = $this->client->post(
                $this->getPaymentOperationPath($paymentId, self::REFUNDS),
                json_encode($refundPayment)
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t refund payment with id: %s', $paymentId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $contents);
        }

        return RefundPaymentResult::fromJson($contents);
    }

    /**
     * @throws PaymentApiException
     * @throws \JsonException
     */
    public function cancelPendingRefund(string $refundId): void
    {
        try {
            $response = $this->client->post(
                \sprintf(
                    '%s/%s%s',
                    self::PENDING_REFUNDS_ENDPOINT,
                    $refundId,
                    self::REFUND_CANCELS
                ),
                ''
            );
        } catch (HttpClientException $httpClientException) {
            throw new PaymentApiException(
                \sprintf('Couldn\'t cancel pending refund with id: %s', $refundId),
                $httpClientException->getCode(),
                $httpClientException
            );
        }

        $code = $response->getStatusCode();

        if (!$this->isSuccessCode($code)) {
            throw $this->createPaymentApiException($code, $response->getBody()->getContents());
        }
    }

    private function getPaymentOperationPath(string $paymentId, string $operation): string
    {
        return \sprintf('%s/%s%s', self::PAYMENTS_ENDPOINT, $paymentId, $operation);
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
