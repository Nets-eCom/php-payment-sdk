<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;
use NexiCheckout\Model\Webhook\Data\Amount;
use NexiCheckout\Model\Webhook\Data\CancelFailedData;
use NexiCheckout\Model\Webhook\Data\Error;
use NexiCheckout\Model\Webhook\Data\OrderItem;
use NexiCheckout\Model\Webhook\Shared\Data;

class CancelFailed implements WebhookInterface, JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $id,
        private readonly EventNameEnum $event,
        private readonly \DateTimeInterface $timestamp,
        private readonly int $merchantId,
        private readonly CancelFailedData $data
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

    public static function fromJson(string $string): CancelFailed
    {
        $payload = self::jsonDeserialize($string);

        return new self(
            $payload['id'],
            EventNameEnum::from($payload['event']),
            new \DateTimeImmutable($payload['timestamp']),
            $payload['merchantId'],
            self::createData($payload['data'])
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
     *     error: array{
     *         code: string,
     *         message: string,
     *         source: string
     *     },
     *     cancelId: string,
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
     *     myReference: ?string,
     * } $data
     */
    private static function createData(array $data): CancelFailedData
    {
        return new CancelFailedData(
            $data['paymentId'],
            new Error(...$data['error']),
            $data['cancelId'],
            array_map(fn (array $orderItem): OrderItem => new OrderItem(...$orderItem), $data['orderItems']),
            new Amount(...$data['amount']),
            $data['myReference'] ?? null,
        );
    }
}
