<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class WebhookBuilder
{
    use JsonDeserializeTrait;

    public static function fromJson(string $string): WebhookInterface
    {
        $payload = self::jsonDeserialize($string);
        $eventName = EventNameEnum::from($payload['event']);

        return match ($eventName) {
            EventNameEnum::PAYMENT_CREATED => PaymentCreated::fromJson($string),
            EventNameEnum::PAYMENT_RESERVATION_CREATED => ReservationCreatedV1::fromJson($string),
            EventNameEnum::PAYMENT_RESERVATION_CREATED_V2 => ReservationCreated::fromJson($string),
            EventNameEnum::PAYMENT_RESERVATION_FAILED => ReservationFailed::fromJson($string),
            EventNameEnum::PAYMENT_CHECKOUT_COMPLETED => CheckoutCompleted::fromJson($string),
            EventNameEnum::PAYMENT_CHARGE_CREATED => ChargeCreated::fromJson($string),
            EventNameEnum::PAYMENT_CHARGE_FAILED => ChargeFailed::fromJson($string),
            EventNameEnum::PAYMENT_REFUND_INITIATED => RefundInitiated::fromJson($string),
            EventNameEnum::PAYMENT_REFUND_FAILED => RefundFailed::fromJson($string),
            EventNameEnum::PAYMENT_REFUND_COMPLETED => RefundCompleted::fromJson($string),
            EventNameEnum::PAYMENT_CANCEL_CREATED => CancelCreated::fromJson($string),
            EventNameEnum::PAYMENT_CANCEL_FAILED => CancelFailed::fromJson($string),
        };
    }
}
