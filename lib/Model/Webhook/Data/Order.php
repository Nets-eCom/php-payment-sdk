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
        private readonly array $orderItems,
        private readonly ?string $reference = null,
    ) {
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    /**
     * @return array<OrderItem>
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }
}
