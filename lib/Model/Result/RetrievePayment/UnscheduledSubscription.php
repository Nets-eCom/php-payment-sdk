<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment;

class UnscheduledSubscription
{
    public function __construct(private readonly string $unscheduledSubscriptionId)
    {
    }

    public function getUnscheduledSubscriptionId(): string
    {
        return $this->unscheduledSubscriptionId;
    }
}
