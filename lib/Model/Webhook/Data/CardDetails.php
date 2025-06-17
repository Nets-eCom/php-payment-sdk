<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Data\CardDetails\ThreeDSecure;

class CardDetails
{
    public function __construct(
        private readonly string $creditDebitIndicator,
        private readonly string $expiryMonth,
        private readonly string $expiryYear,
        private readonly string $issuerCountry,
        private readonly string $truncatedPan,
        private readonly ?ThreeDSecure $threeDSecure = null
    ) {
    }

    public function getCreditDebitIndicator(): string
    {
        return $this->creditDebitIndicator;
    }

    public function getExpiryMonth(): string
    {
        return $this->expiryMonth;
    }

    public function getExpiryYear(): string
    {
        return $this->expiryYear;
    }

    public function getIssuerCountry(): string
    {
        return $this->issuerCountry;
    }

    public function getTruncatedPan(): string
    {
        return $this->truncatedPan;
    }

    public function getThreeDSecure(): ?ThreeDSecure
    {
        return $this->threeDSecure;
    }
}
