<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\SubscriptionCharges;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class SingleSubscriptionCharge implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $paymentId,
        private readonly string $chargeId,
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

    public static function fromJson(string $string): self
    {
        return new self(...self::jsonDeserialize($string));
    }
}
