<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

final class PaymentMethods
{
    public function __construct(
        private readonly ?string $currency = null,
        private readonly ?bool $enabled = null,
        private readonly ?string $merchantNumber = null,
    ) {
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function getMerchantNumber(): ?string
    {
        return $this->merchantNumber;
    }
}
