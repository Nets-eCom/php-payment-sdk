<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;
use NexiCheckout\Model\Webhook\Data\Amount;
use NexiCheckout\Model\Webhook\Data\CardDetails;
use NexiCheckout\Model\Webhook\Data\CardDetails\ThreeDSecure;
use NexiCheckout\Model\Webhook\Data\Consumer;
use NexiCheckout\Model\Webhook\Data\Consumer\Address;
use NexiCheckout\Model\Webhook\Data\Consumer\PhoneNumber;
use NexiCheckout\Model\Webhook\Data\ReservationCreatedV1Data;

class ReservationCreatedV1 implements WebhookInterface, JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $id,
        private readonly \DateTimeInterface $timestamp,
        private readonly int $merchantId,
        private readonly EventNameEnum $event,
        private readonly ReservationCreatedV1Data $data,
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

    public function getData(): ReservationCreatedV1Data
    {
        return $this->data;
    }

    public static function fromJson(string $string): ReservationCreatedV1
    {
        $payload = self::jsonDeserialize($string);

        return new self(
            id: $payload['id'],
            timestamp: new \DateTimeImmutable($payload['timestamp']),
            merchantId: $payload['merchantId'],
            event: EventNameEnum::from($payload['event']),
            data: self::createReservationCreated($payload['data'])
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createReservationCreated(array $data): ReservationCreatedV1Data
    {
        return new ReservationCreatedV1Data(
            paymentId: $data['paymentId'],
            paymentMethod: $data['paymentMethod'],
            paymentType: $data['paymentType'],
            consumer: self::createConsumer($data['consumer']),
            amount: new Amount(...$data['amount']),
            cardDetails: isset($data['cardDetails']) ? self::createCardDetails($data['cardDetails']) : null,
            myReference: $data['myReference'] ?? null,
            reserveId : $data['reserveId'] ?? null,
            reservationReference : $data['reservationReference'] ?? null,
            reconciliationReference: $data['reconciliationReference'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $consumer
     */
    private static function createConsumer(array $consumer): Consumer
    {
        $phoneNumber = $consumer['phoneNumber'] ?? null;

        return new Consumer(
            email: $consumer['email'],
            ip: $consumer['ip'],
            country: $consumer['country'],
            billingAddress: self::createAddress($consumer['billingAddress'] ?? []),
            shippingAddress: self::createAddress($consumer['shippingAddress'] ?? []),
            firstName: $consumer['firstName'] ?? null,
            lastName: $consumer['lastName'] ?? null,
            phoneNumber: $phoneNumber ? new PhoneNumber($phoneNumber['prefix'], $phoneNumber['number']) : null,
            merchantReference: $consumer['merchantReference'] ?? null,
        );
    }

    /**
     * @param array<string, string> $address
     */
    private static function createAddress(array $address): Address
    {
        return new Address(
            addressLine1: $address['addressLine1'] ?? null,
            addressLine2: $address['addressLine2'] ?? null,
            city: $address['city'] ?? null,
            country: $address['country'] ?? null,
            postcode: $address['postcode'] ?? null,
            receiverLine: $address['receiverLine'] ?? null,
        );
    }

    /**
     * @param array<string, mixed> $cardDetails
     */
    private static function createCardDetails(array $cardDetails): CardDetails
    {
        return new CardDetails(
            creditDebitIndicator: $cardDetails['creditDebitIndicator'],
            expiryMonth: $cardDetails['expiryMonth'],
            expiryYear: $cardDetails['expiryYear'],
            issuerCountry: $cardDetails['issuerCountry'],
            truncatedPan: $cardDetails['truncatedPan'],
            threeDSecure: $cardDetails['threeDSecure'] ? new ThreeDSecure(...$cardDetails['threeDSecure']) : null,
        );
    }
}
