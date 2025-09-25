<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

final class PaymentMethods
{
    public function __construct(
        private readonly ?string $merchantNumber = null,
        private readonly ?string $currency = null,
        private readonly ?bool $enabled = null
    ) {
    }

    public function getMerchantNumber(): ?string
    {
        return $this->merchantNumber;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }
}
