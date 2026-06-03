<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrieveBulkUnscheduledCharges;

use NexiCheckout\Model\Result\SubscriptionCharges\ChargeStatusEnum;

class UnscheduledCharge
{
    public function __construct(
        private readonly string $unscheduledSubscriptionId,
        private readonly ChargeStatusEnum $status,
        private readonly ?string $paymentId = null,
        private readonly ?string $chargeId = null,
        private readonly ?string $message = null,
        private readonly ?string $code = null,
        private readonly ?string $source = null,
        private readonly ?string $externalReference = null,
    ) {
    }

    public function getUnscheduledSubscriptionId(): string
    {
        return $this->unscheduledSubscriptionId;
    }

    public function getStatus(): ChargeStatusEnum
    {
        return $this->status;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }

    public function getChargeId(): ?string
    {
        return $this->chargeId;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }
}
