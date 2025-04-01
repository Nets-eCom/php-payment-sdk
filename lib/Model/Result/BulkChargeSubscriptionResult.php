<?php

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class BulkChargeSubscriptionResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(private readonly string $bulkId)
    {
    }

    public function getBulkId(): string
    {
        return $this->bulkId;
    }

    public static function fromJson(string $string): BulkChargeSubscriptionResult
    {
        return new self(...self::jsonDeserialize($string));
    }
}
