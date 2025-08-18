<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;
use NexiCheckout\Model\Webhook\Data\Amount;
use NexiCheckout\Model\Webhook\Data\Order;
use NexiCheckout\Model\Webhook\Data\OrderItem;
use NexiCheckout\Model\Webhook\Data\PaymentCreatedData;
use NexiCheckout\Model\Webhook\Shared\Data;

class PaymentCreated implements WebhookInterface, JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $id,
        private readonly \DateTimeInterface $timestamp,
        private readonly int $merchantId,
        private readonly EventNameEnum $event,
        private readonly PaymentCreatedData $data,
    ) {
    }

    public static function fromJson(string $string): PaymentCreated
    {
        $payload = self::jsonDeserialize($string);

        return new self(
            id: $payload['id'],
            timestamp: new \DateTimeImmutable($payload['timestamp']),
            merchantId: $payload['merchantId'],
            event: EventNameEnum::from($payload['event']),
            data: self::createPaymentCreatedData($payload['data'])
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

    /**
     * @param array<string, mixed> $data
     */
    private static function createOrder(array $data): Order
    {
        return new Order(
            new Amount(...$data['amount']),
            array_map(fn (array $orderItem): OrderItem => new OrderItem(...$orderItem), $data['orderItems']),
            $data['reference'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createPaymentCreatedData(array $data): PaymentCreatedData
    {
        return new PaymentCreatedData(
            paymentId: $data['paymentId'],
            order: self::createOrder($data['order']),
            myReference: $data['myReference'] ?? null,
            subscriptionId: $data['subscriptionId'] ?? null,
        );
    }
}
