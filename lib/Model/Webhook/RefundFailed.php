<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\InvoiceDatailsTrait;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;
use NexiCheckout\Model\Webhook\Data\Amount;
use NexiCheckout\Model\Webhook\Data\Error;
use NexiCheckout\Model\Webhook\Data\RefundFailedData;
use NexiCheckout\Model\Webhook\Shared\Data;

class RefundFailed implements WebhookInterface, JsonDeserializeInterface
{
    use JsonDeserializeTrait;
    use InvoiceDatailsTrait;

    public function __construct(
        private readonly string $id,
        private readonly \DateTimeInterface $timestamp,
        private readonly int $merchantId,
        private readonly EventNameEnum $event,
        private readonly RefundFailedData $data,
    ) {
    }

    public static function fromJson(string $string): RefundFailed
    {
        $payload = self::jsonDeserialize($string);

        return new self(
            $payload['id'],
            new \DateTimeImmutable($payload['timestamp']),
            $payload['merchantId'],
            EventNameEnum::from($payload['event']),
            self::createData($payload['data'])
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTimestamp(): \DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getMerchantId(): int
    {
        return $this->merchantId;
    }

    public function getEvent(): EventNameEnum
    {
        return $this->event;
    }

    public function getData(): Data
    {
        return $this->data;
    }

    /**
     * @param array{
     *     paymentId: string,
     *     error: array{
     *         code: string,
     *         message: string,
     *         source: string
     *     },
     *     refundId: string,
     *     reconciliationReference: string,
     *     amount: array{
     *          amount: int,
     *          currency: string
     *     },
     *     invoiceDetails?: array<string, string>
     * } $data
     */
    private static function createData(array $data): RefundFailedData
    {
        return new RefundFailedData(
            $data['paymentId'],
            new Error(...$data['error']),
            $data['refundId'],
            $data['reconciliationReference'],
            new Amount(...$data['amount']),
            isset($data['invoiceDetails']) ? self::createInvoiceDetails($data['invoiceDetails']) : null
        );
    }
}
