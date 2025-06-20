<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;
use NexiCheckout\Model\Webhook\Data\Amount;
use NexiCheckout\Model\Webhook\Data\Error;
use NexiCheckout\Model\Webhook\Data\OrderItem;
use NexiCheckout\Model\Webhook\Data\ReservationFailedData;

class ReservationFailed implements WebhookInterface, JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $id,
        private readonly \DateTimeInterface $timestamp,
        private readonly int $merchantId,
        private readonly EventNameEnum $event,
        private readonly ReservationFailedData $data,
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

    public function getData(): ReservationFailedData
    {
        return $this->data;
    }

    public static function fromJson(string $string): self
    {
        $payload = self::jsonDeserialize($string);

        return new self(
            id: $payload['id'],
            timestamp: new \DateTimeImmutable($payload['timestamp']),
            merchantId: $payload['merchantId'],
            event: EventNameEnum::from($payload['event']),
            data: self::createReservationFailedData($payload['data']),
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createReservationFailedData(array $data): ReservationFailedData
    {
        return new ReservationFailedData(
            paymentId: $data['paymentId'],
            error: new Error(...$data['error']),
            orderItems: array_map(fn (array $orderItem): OrderItem => new OrderItem(...$orderItem), $data['orderItems']),
            amount: new Amount(...$data['amount']),
        );
    }
}
