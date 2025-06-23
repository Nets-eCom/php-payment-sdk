<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Shared\Data;

class CancelFailedData extends Data
{
    /**
     * @param list<OrderItem> $items
     */
    public function __construct(
        string $paymentId,
        private readonly Error $error,
        private readonly string $cancelId,
        private readonly array $items,
        private readonly Amount $amount,
        private readonly ?string $myReference = null,
    ) {
        parent::__construct($paymentId);
    }

    public function getError(): Error
    {
        return $this->error;
    }

    public function getCancelId(): string
    {
        return $this->cancelId;
    }

    /**
     * @return list<OrderItem>
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getMyReference(): ?string
    {
        return $this->myReference;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }
}
