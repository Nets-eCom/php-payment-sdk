<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment;

class Charge
{
    /**
     * @param array<Item> $orderItems
     */
    public function __construct(
        private readonly string $chargeId,
        private readonly int $amount,
        private readonly \DateTimeInterface $created,
        private readonly array $orderItems,
        private readonly int $surchargeAmount,
    ) {
    }

    public function getChargeId(): string
    {
        return $this->chargeId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCreated(): \DateTimeInterface
    {
        return $this->created;
    }

    /**
     * @return array<Item>
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    public function getSurchargeAmount(): int
    {
        return $this->surchargeAmount;
    }
}
