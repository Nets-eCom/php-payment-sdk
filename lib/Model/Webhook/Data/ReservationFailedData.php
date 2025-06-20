<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Shared\Data;

class ReservationFailedData extends Data
{
    /**
     * @param OrderItem[] $orderItems
     */
    public function __construct(
        string $paymentId,
        private readonly Error $error,
        private readonly array $orderItems,
        private readonly Amount $amount,
    ) {
        parent::__construct($paymentId);
    }

    public function getError(): Error
    {
        return $this->error;
    }

    /**
     * @return OrderItem[]
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }
}
