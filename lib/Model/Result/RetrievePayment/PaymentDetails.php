<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment;

use NexiCheckout\Model\Result\RetrievePayment\PaymentDetails\CardDetails;
use NexiCheckout\Model\Result\RetrievePayment\PaymentDetails\InvoiceDetails;
use NexiCheckout\Model\Result\RetrievePayment\PaymentDetails\PaymentTypeEnum;

class PaymentDetails
{
    public function __construct(
        private readonly ?PaymentTypeEnum $paymentType,
        private readonly ?string $paymentMethod,
        private readonly ?InvoiceDetails $invoiceDetails,
        private readonly ?CardDetails $cardDetails
    ) {
    }

    public function getPaymentType(): ?PaymentTypeEnum
    {
        return $this->paymentType;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function getInvoiceDetails(): ?InvoiceDetails
    {
        return $this->invoiceDetails;
    }

    public function getCardDetails(): ?CardDetails
    {
        return $this->cardDetails;
    }
}
