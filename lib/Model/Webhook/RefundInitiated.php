<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;
use NexiCheckout\Model\Webhook\Data\Amount;
use NexiCheckout\Model\Webhook\Data\OrderItem;
use NexiCheckout\Model\Webhook\Data\RefundInitiatedData;
use NexiCheckout\Model\Webhook\Shared\Data;

class RefundInitiated implements WebhookInterface, JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $id,
        private readonly int $merchantId,
        private readonly EventNameEnum $event,
        private readonly \DateTimeInterface $timestamp,
        private readonly RefundInitiatedData $data
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

    public static function fromJson(string $string): RefundInitiated
    {
        $payload = self::jsonDeserialize($string);

        return new self(
            $payload['id'],
            $payload['merchantId'],
            EventNameEnum::from($payload['event']),
            new \DateTimeImmutable($payload['timestamp']),
            self::createRefundInitiatedData($payload['data'])
        );
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
     *     orderItems: list<array{
     *         reference: string,
     *         name: string,
     *         quantity: int,
     *         unit: string,
     *         unitPrice: int,
     *         taxRate: int,
     *         taxAmount: int,
     *         grossTotalAmount: int,
     *         netTotalAmount: int
     *     }>,
     *     amount: array{
     *         amount: int,
     *         currency: string
     *     },
     *     reconciliationReference: string,
     *     executed: string,
     *     paymentActionId: string,
     * } $data
     */
    private static function createRefundInitiatedData(array $data): RefundInitiatedData
    {
        return new RefundInitiatedData(
            $data['paymentId'],
            array_map(fn (array $orderItem): OrderItem => new OrderItem(...$orderItem), $data['orderItems']),
            new Amount(...$data['amount']),
            $data['reconciliationReference'],
            new \DateTimeImmutable($data['executed']),
            $data['paymentActionId'],
        );
    }
}
