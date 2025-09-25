<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\PaymentMethodsResult;

final class PaymentMethod
{
    public function __construct(
        private readonly ?string $name,
        private readonly ?string $paymentType,
        private readonly ?string $currency,
        private readonly bool $enabled
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
