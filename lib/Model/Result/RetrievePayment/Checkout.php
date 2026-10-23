<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment;

class Checkout
{
    public function __construct(
        private readonly string $url,
        private readonly ?string $cancelUrl = null,
        private readonly ?\DateTimeInterface $expiresAt = null,
    ) {
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getCancelUrl(): ?string
    {
        return $this->cancelUrl;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }
}
