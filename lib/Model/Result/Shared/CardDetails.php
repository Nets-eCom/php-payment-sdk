<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\Shared;

class CardDetails
{
    public function __construct(private readonly ?string $maskedPan, private readonly ?string $expiryDate)
    {
    }

    /**
     * @param array{maskedPan?: string, expiryDate?: string} $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['maskedPan'] ?? null,
            $data['expiryDate'] ?? null,
        );
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
