<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Shared\Data;

class ReservationCreatedData extends Data
{
    public function __construct(
        string $paymentId,
        private readonly string $paymentMethod,
        private readonly string $paymentType,
        private readonly Amount $amount,
        private readonly ?string $myReference = null,
        private readonly ?string $subscriptionId = null,
        private readonly ?string $reconciliationReference = null,
    ) {
        parent::__construct($paymentId);
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getMyReference(): ?string
    {
        return $this->myReference;
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function getReconciliationReference(): ?string
    {
        return $this->reconciliationReference;
    }
}
