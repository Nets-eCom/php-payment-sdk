<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment;

class Summary
{
    public function __construct(
        private readonly int $reservedAmount,
        private readonly int $chargedAmount,
        private readonly int $refundedAmount,
        private readonly int $cancelledAmount,
        private readonly int $reservedSurchargeAmount,
        private readonly int $chargedSurchargeAmount,
        private readonly int $refundedSurchargeAmount,
        private readonly int $cancelledSurchargeAmount,
    ) {
    }

    public function getReservedAmount(): int
    {
        return $this->reservedAmount;
    }

    public function getChargedAmount(): int
    {
        return $this->chargedAmount;
    }

    public function getRefundedAmount(): int
    {
        return $this->refundedAmount;
    }

    public function getCancelledAmount(): int
    {
        return $this->cancelledAmount;
    }

    public function getReservedSurchargeAmount(): int
    {
        return $this->reservedSurchargeAmount;
    }

    public function getChargedSurchargeAmount(): int
    {
        return $this->chargedSurchargeAmount;
    }

    public function getRefundedSurchargeAmount(): int
    {
        return $this->refundedSurchargeAmount;
    }

    public function getCancelledSurchargeAmount(): int
    {
        return $this->cancelledSurchargeAmount;
    }
}
