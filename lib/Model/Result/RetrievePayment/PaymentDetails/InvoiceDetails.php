<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment\PaymentDetails;

class InvoiceDetails
{
    public function __construct(private readonly ?string $invoiceNumber)
    {
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }
}
