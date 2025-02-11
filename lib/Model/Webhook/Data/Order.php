<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

class Order
{
    /**
     * @param array<OrderItem> $orderItems
     */
    public function __construct(
        private readonly Amount $amount,
        private readonly string $reference,
        private readonly array $orderItems,
    ) {
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @return array<OrderItem>
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }
}
