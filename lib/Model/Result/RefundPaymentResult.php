<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class RefundPaymentResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(private readonly string $refundId)
    {
    }

    public function getRefundId(): string
    {
        return $this->refundId;
    }

    public static function fromJson(string $string): self
    {
        return new self(...self::jsonDeserialize($string));
    }
}
