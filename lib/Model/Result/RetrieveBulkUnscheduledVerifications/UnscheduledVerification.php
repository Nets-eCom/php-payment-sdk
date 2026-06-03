<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrieveBulkUnscheduledVerifications;

use NexiCheckout\Model\Result\Shared\VerificationStatusEnum;

class UnscheduledVerification
{
    public function __construct(
        private readonly string $unscheduledSubscriptionId,
        private readonly VerificationStatusEnum $verificationStatus,
        private readonly ?string $externalReference = null,
        private readonly ?string $message = null,
        private readonly ?string $code = null,
        private readonly ?string $paymentId = null,
    ) {
    }

    public function getUnscheduledSubscriptionId(): string
    {
        return $this->unscheduledSubscriptionId;
    }

    public function getVerificationStatus(): VerificationStatusEnum
    {
        return $this->verificationStatus;
    }

    public function getExternalReference(): ?string
    {
        return $this->externalReference;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getPaymentId(): ?string
    {
        return $this->paymentId;
    }
}
