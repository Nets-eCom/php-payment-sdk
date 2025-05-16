<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;
use NexiCheckout\Model\Webhook\Data\Amount;
use NexiCheckout\Model\Webhook\Data\ReservationCreatedData;

class ReservationCreated implements WebhookInterface, JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $id,
        private readonly \DateTimeInterface $timestamp,
        private readonly int $merchantNumber,
        private readonly EventNameEnum $event,
        private readonly ReservationCreatedData $data,
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

    public function getMerchantNumber(): int
    {
        return $this->merchantNumber;
    }

    public function getEvent(): EventNameEnum
    {
        return $this->event;
    }

    public function getData(): ReservationCreatedData
    {
        return $this->data;
    }

    public static function fromJson(string $string): ReservationCreated
    {
        $payload = self::jsonDeserialize($string);

        return new self(
            id: $payload['id'],
            timestamp: new \DateTimeImmutable($payload['timestamp']),
            merchantNumber: $payload['merchantNumber'],
            event: EventNameEnum::from($payload['event']),
            data: self::createReservationCreated($payload['data'])
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createReservationCreated(array $data): ReservationCreatedData
    {
        return new ReservationCreatedData(
            paymentId: $data['paymentId'],
            paymentMethod: $data['paymentMethod'],
            paymentType: $data['paymentType'],
            amount: new Amount(...$data['amount']),
            myReference: $data['myReference'] ?? null,
            subscriptionId: $data['subscriptionId'] ?? null,
            reconciliationReference: $data['reconciliationReference'] ?? null,
        );
    }
}
