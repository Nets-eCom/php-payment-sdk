<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Shared\Data;

class CheckoutCompletedData extends Data
{
    public function __construct(
        string $paymentId,
        private readonly Order $order,
        private readonly Consumer $consumer
    ) {
        parent::__construct($paymentId);
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getConsumer(): Consumer
    {
        return $this->consumer;
    }
}
