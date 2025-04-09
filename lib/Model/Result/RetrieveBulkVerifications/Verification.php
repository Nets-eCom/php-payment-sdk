<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrieveBulkVerifications;

class Verification
{
    public function __construct(
        private readonly string $subscriptionId,
        private readonly VerificationStatusEnum $processingStatusEnum,
        private readonly ?string $externalReference = null,
        private readonly ?string $message = null,
        private readonly ?string $code = null,
        private readonly ?string $paymentId = null,
    ) {
    }

    public function getSubscriptionId(): string
    {
        return $this->subscriptionId;
    }

    public function getVerificationStatusEnum(): VerificationStatusEnum
    {
        return $this->processingStatusEnum;
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
