<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\InvoiceDatailsTrait;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;
use NexiCheckout\Model\Webhook\Data\Amount;
use NexiCheckout\Model\Webhook\Data\ChargeFailedData;
use NexiCheckout\Model\Webhook\Data\Error;
use NexiCheckout\Model\Webhook\Data\OrderItem;

class ChargeFailed implements WebhookInterface, JsonDeserializeInterface
{
    use JsonDeserializeTrait;
    use InvoiceDatailsTrait;

    public function __construct(
        private readonly string $id,
        private readonly EventNameEnum $event,
        private readonly \DateTimeInterface $timestamp,
        private readonly int $merchantId,
        private readonly ChargeFailedData $data,
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

    public function getData(): ChargeFailedData
    {
        return $this->data;
    }

    public static function fromJson(string $string): ChargeFailed
    {
        $payload = self::jsonDeserialize($string);

        return new self(
            id: $payload['id'],
            event: EventNameEnum::from($payload['event']),
            timestamp: new \DateTimeImmutable($payload['timestamp']),
            merchantId: $payload['merchantId'],
            data: self::createChargeFailedData($payload['data'])
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createChargeFailedData(array $data): ChargeFailedData
    {
        return new ChargeFailedData(
            $data['paymentId'],
            new Error(...$data['error']),
            $data['chargeId'],
            $data['reconciliationReference'],
            array_map(fn (array $orderItem) => new OrderItem(...$orderItem), $data['orderItems']),
            new Amount(...$data['amount']),
            isset($data['invoiceDetails']) ? self::createInvoiceDetails($data['invoiceDetails']) : null
        );
    }
}
