<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Result\RetrieveSubscription\ImportError;
use NexiCheckout\Model\Result\RetrieveSubscription\PaymentDetails;
use NexiCheckout\Model\Result\Shared\CardDetails;
use NexiCheckout\Model\Result\Shared\PaymentTypeEnum;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class RetrieveSubscriptionResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $subscriptionId,
        private readonly int $interval,
        private readonly \DateTimeInterface $endDate,
        private readonly PaymentDetails $paymentDetails,
        private readonly ?int $frequency = null,
        private readonly ?ImportError $importError = null,
    ) {
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function getInterval(): int
    {
        return $this->interval;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function getPaymentDetails(): PaymentDetails
    {
        return $this->paymentDetails;
    }

    public function getFrequency(): ?int
    {
        return $this->frequency;
    }

    public function getImportError(): ?ImportError
    {
        return $this->importError;
    }

    public static function fromJson(string $string): RetrieveSubscriptionResult
    {
        $data = self::jsonDeserialize($string);

        return new RetrieveSubscriptionResult(
            $data['subscriptionId'],
            $data['interval'],
            isset($data['endDate']) ? new \DateTime($data['endDate']) : null,
            self::createPaymentDetails($data['paymentDetails']),
            $data['frequency'] ?? null,
            isset($data['importError']) ? self::createImportError($data['importError']) : null,
        );
    }

    /**
     * @param array{
     *     importStepsResponseCode: string,
     *     importStepsResponseSource: string,
     *     importStepsResponseText: string,
     * } $data
     */
    private static function createImportError(array $data): ImportError
    {
        return new ImportError(
            $data['importStepsResponseCode'],
            $data['importStepsResponseSource'],
            $data['importStepsResponseText'],
        );
    }

    /**
     * @param array{
     *     paymentType: string,
     *     paymentMethod: string,
     *     cardDetails: array{
     *         maskedPan: string,
     *         expiryDate: string
     *     }
     * } $data
     */
    private static function createPaymentDetails(array $data): PaymentDetails
    {
        return new PaymentDetails(
            PaymentTypeEnum::tryFrom($data['paymentType']),
            $data['paymentMethod'],
            new CardDetails(...$data['cardDetails']),
        );
    }
}
