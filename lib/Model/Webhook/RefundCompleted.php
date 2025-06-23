<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\InvoiceDatailsTrait;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;
use NexiCheckout\Model\Webhook\Data\Amount;
use NexiCheckout\Model\Webhook\Data\RefundCompletedData;

class RefundCompleted implements WebhookInterface, JsonDeserializeInterface
{
    use JsonDeserializeTrait;
    use InvoiceDatailsTrait;

    public function __construct(
        private readonly string $id,
        private readonly \DateTimeInterface $timestamp,
        private readonly int $merchantId,
        private readonly EventNameEnum $event,
        private readonly RefundCompletedData $data,
    ) {
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

    public function getData(): RefundCompletedData
    {
        return $this->data;
    }

    public static function fromJson(string $string): RefundCompleted
    {
        $payload = self::jsonDeserialize($string);

        return new self(
            $payload['id'],
            new \DateTimeImmutable($payload['timestamp']),
            $payload['merchantId'],
            EventNameEnum::from($payload['event']),
            self::createRefundCompleted($payload['data'])
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createRefundCompleted(array $data): RefundCompletedData
    {
        return new RefundCompletedData(
            paymentId: $data['paymentId'],
            refundId: $data['refundId'],
            amount: new Amount(...$data['amount']),
            invoiceDetails: isset($data['invoiceDetails']) ? self::createInvoiceDetails($data['invoiceDetails']) : null,
        );
    }
}
