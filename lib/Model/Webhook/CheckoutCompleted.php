<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;
use NexiCheckout\Model\Webhook\Data\Amount;
use NexiCheckout\Model\Webhook\Data\CheckoutCompletedData;
use NexiCheckout\Model\Webhook\Data\Consumer;
use NexiCheckout\Model\Webhook\Data\Consumer\Address;
use NexiCheckout\Model\Webhook\Data\Consumer\PhoneNumber;
use NexiCheckout\Model\Webhook\Data\Order;
use NexiCheckout\Model\Webhook\Data\OrderItem;
use NexiCheckout\Model\Webhook\Shared\Data;

class CheckoutCompleted implements WebhookInterface, JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $id,
        private readonly int $merchantId,
        private readonly \DateTimeInterface $timestamp,
        private readonly EventNameEnum $eventName,
        private readonly Data $data
    ) {
    }

    public static function fromJson(string $string): CheckoutCompleted
    {
        $payload = self::jsonDeserialize($string);

        return new self(
            $payload['id'],
            $payload['merchantId'],
            new \DateTimeImmutable($payload['timestamp']),
            EventNameEnum::PAYMENT_CHECKOUT_COMPLETED,
            self::createCheckoutCompleteData($payload['data'])
        );
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMerchantId(): int
    {
        return $this->merchantId;
    }

    public function getTimestamp(): \DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getEvent(): EventNameEnum
    {
        return $this->eventName;
    }

    public function getData(): Data
    {
        return $this->data;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createCheckoutCompleteData(array $data): CheckoutCompletedData
    {
        $order = $data['order'];
        $consumer = $data['consumer'];
        $phoneNumber = $consumer['phoneNumber'] ?? null;

        return new CheckoutCompletedData(
            $data['paymentId'],
            new Order(
                new Amount(...$order['amount']),
                $order['reference'],
                array_map(fn (array $orderItem): OrderItem => new OrderItem(...$orderItem), $order['orderItems'])
            ),
            new Consumer(
                $consumer['firstName'],
                $consumer['lastName'],
                $consumer['country'],
                $consumer['email'],
                $consumer['ip'],
                self::createAddress($consumer['billingAddress']),
                self::createAddress($consumer['shippingAddress']),
                $phoneNumber ? new PhoneNumber($phoneNumber['prefix'], $phoneNumber['number']) : null,
            )
        );
    }

    /**
     * @param array<string, string> $address
     */
    private static function createAddress(array $address): Address
    {
        return new Address(
            $address['addressLine1'],
            $address['addressLine2'],
            $address['city'],
            $address['country'],
            $address['postcode'],
            $address['receiverLine'],
        );
    }
}
