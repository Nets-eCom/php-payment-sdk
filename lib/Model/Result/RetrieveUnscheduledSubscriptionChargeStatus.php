<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class RetrieveUnscheduledSubscriptionChargeStatus implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $paymentId,
        private readonly string $chargeId,
        private readonly bool $completed,
    ) {
    }

    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    public function getChargeId(): string
    {
        return $this->chargeId;
    }

    public function getCompleted(): bool
    {
        return $this->completed;
    }

    public static function fromJson(string $string): self
    {
        return new self(...self::jsonDeserialize($string));
    }
}
