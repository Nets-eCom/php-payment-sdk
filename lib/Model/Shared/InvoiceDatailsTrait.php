<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Shared;

use NexiCheckout\Model\Webhook\Data\InvoiceDetails;

trait InvoiceDatailsTrait
{
    /**
     * @param array{
     *     invoiceNumber: string,
     *     accountNumber: ?string,
     *     distributionType: ?string,
     *     invoiceDueDate: ?string,
     *     ocrOrkid: ?string,
     *     ourReference: ?string,
     *     yourReference: ?string
     * } $data
     */
    private static function createInvoiceDetails(array $data): InvoiceDetails
    {
        return new InvoiceDetails(
            $data['invoiceNumber'],
            $data['accountNumber'] ?? null,
            $data['distributionType'] ?? null,
            $data['invoiceDueDate'] ? new \DateTimeImmutable($data['invoiceDueDate']) : null,
            $data['ocrOrkid'] ?? null,
            $data['ourReference'] ?? null,
            $data['yourReference'] ?? null
        );
    }
}
