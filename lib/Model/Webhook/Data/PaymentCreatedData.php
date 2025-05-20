<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Shared\Data;

class PaymentCreatedData extends Data
{
    public function __construct(
        string $paymentId,
        private readonly Order $order,
        private readonly ?string $myReference = null,
        private readonly ?string $subscriptionId = null,
    ) {
        parent::__construct($paymentId);
    }

    public function getMyReference(): ?string
    {
        return $this->myReference;
    }

    public function getSubscriptionId(): ?string
    {
        return $this->subscriptionId;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }
}
