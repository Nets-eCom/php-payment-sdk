<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Shared\Data;

class RefundFailedData extends Data
{
    public function __construct(
        string $paymentId,
        private readonly Error $error,
        private readonly string $refundId,
        private readonly string $reconciliationReference,
        private readonly Amount $amount,
        private readonly ?InvoiceDetails $invoiceDetails = null,
    ) {
        parent::__construct($paymentId);
    }

    public function getError(): Error
    {
        return $this->error;
    }

    public function getRefundId(): string
    {
        return $this->refundId;
    }

    public function getReconciliationReference(): string
    {
        return $this->reconciliationReference;
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
