<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

final class CardPaymentResult extends PaymentResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        string $paymentId,
        private readonly ?string $idempotencyKey = null,
    ) {
        parent::__construct($paymentId);
    }

    public function getIdempotencyKey(): ?string
    {
        return $this->idempotencyKey;
    }

    public static function fromJson(string $string): CardPaymentResult
    {
        return new self(...self::jsonDeserializeToClassVars($string));
    }
}
