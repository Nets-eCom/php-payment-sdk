<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment\PaymentDetails;

class CardDetails
{
    public function __construct(private readonly ?string $maskedPan, private readonly ?string $expiryDate)
    {
    }

    public function getMaskedPan(): ?string
    {
        return $this->maskedPan;
    }

    public function getExpiryDate(): ?string
    {
        return $this->expiryDate;
    }
}
