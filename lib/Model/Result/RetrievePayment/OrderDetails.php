<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment;

class OrderDetails
{
    public function __construct(
        private readonly int $amount,
        private readonly string $currency,
        private readonly ?string $reference = null,
    ) {
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }
}
