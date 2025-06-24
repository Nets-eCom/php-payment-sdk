<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Shared\Data;

class RefundInitiatedData extends Data
{
    /**
     * @param list<OrderItem> $items
     */
    public function __construct(
        string $paymentId,
        private readonly array $items,
        private readonly Amount $amount,
        private readonly string $reconciliationReference,
        private readonly \DateTimeInterface $executed,
        private readonly string $paymentActionId,
    ) {
        parent::__construct($paymentId);
    }

    /**
     * @return list<OrderItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getReconciliationReference(): string
    {
        return $this->reconciliationReference;
    }

    public function getExecuted(): \DateTimeInterface
    {
        return $this->executed;
    }

    public function getPaymentActionId(): string
    {
        return $this->paymentActionId;
    }
}
