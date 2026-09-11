<?php

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class VerifyUnscheduledSubscriptionsResult extends VerifySubscriptionsResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public static function fromJson(string $string): VerifyUnscheduledSubscriptionsResult
    {
        return new self(...self::jsonDeserializeToClassVars($string));
    }
}
