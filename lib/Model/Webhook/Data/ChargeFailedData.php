<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Shared\Data;

class ChargeFailedData extends Data
{
    /**
     * @param OrderItem[] $orderItems
     */
    public function __construct(
        string $paymentId,
        private readonly Error $error,
        private readonly string $chargeId,
        private readonly string $reconciliationReference,
        private readonly array $orderItems,
        private readonly Amount $amount,
        private readonly ?InvoiceDetails $invoiceDetails = null,
    ) {
        parent::__construct($paymentId);
    }

    public function getError(): Error
    {
        return $this->error;
    }

    public function getChargeId(): string
    {
        return $this->chargeId;
    }

    public function getReconciliationReference(): string
    {
        return $this->reconciliationReference;
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

    public function getInvoiceDetails(): ?InvoiceDetails
    {
        return $this->invoiceDetails;
    }
}
