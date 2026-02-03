<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\Payment;

use NexiCheckout\Model\Result\PaymentResult;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class PaymentWithEmbeddedCheckoutResult extends PaymentResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public static function fromJson(string $string): PaymentWithEmbeddedCheckoutResult
    {
        return new self(...self::jsonDeserialize($string));
    }
}
