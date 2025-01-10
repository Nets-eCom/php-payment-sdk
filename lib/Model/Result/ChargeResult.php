<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Result\ChargeResult\Invoice;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

final class ChargeResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $chargeId,
        private readonly ?Invoice $invoice = null
    ) {
    }

    public function getChargeId(): string
    {
        return $this->chargeId;
    }

    public function getInvoice(): ?Invoice
    {
        return $this->invoice;
    }

    public static function fromJson(string $string): ChargeResult
    {
        return new self(...self::jsonDeserialize($string));
    }
}
