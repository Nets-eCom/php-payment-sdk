<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

class InvoiceDetails
{
    public function __construct(
        private readonly string $invoiceNumber,
        private readonly ?string $accountNumber = null,
        private readonly ?string $distributionType = null,
        private readonly ?\DateTimeInterface $invoiceDueDate = null,
        private readonly ?string $ocrOrkid = null,
        private readonly ?string $ourReference = null,
        private readonly ?string $yourReference = null,
    ) {
    }

    public function getInvoiceNumber(): string
    {
        return $this->invoiceNumber;
    }

    public function getAccountNumber(): ?string
    {
        return $this->accountNumber;
    }

    public function getDistributionType(): ?string
    {
        return $this->distributionType;
    }

    public function getInvoiceDueDate(): ?\DateTimeInterface
    {
        return $this->invoiceDueDate;
    }

    public function getOcrOrkid(): ?string
    {
        return $this->ocrOrkid;
    }

    public function getOurReference(): ?string
    {
        return $this->ourReference;
    }

    public function getYourReference(): ?string
    {
        return $this->yourReference;
    }
}
