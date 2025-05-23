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
            EventNameEnum::PAYMENT_CREATED => null /* @todo */,
            EventNameEnum::PAYMENT_RESERVATION_CREATED => null /* @todo */,
            EventNameEnum::PAYMENT_RESERVATION_CREATED_V2 => ReservationCreated::fromJson($string),
            EventNameEnum::PAYMENT_RESERVATION_FAILED => null /* @todo */,
            EventNameEnum::PAYMENT_CHECKOUT_COMPLETED => CheckoutCompleted::fromJson($string),
            EventNameEnum::PAYMENT_CHARGE_CREATED => ChargeCreated::fromJson($string),
            EventNameEnum::PAYMENT_CHARGE_FAILED => null /* @todo */,
            EventNameEnum::PAYMENT_REFUND_INITIATED => null /* @todo */,
            EventNameEnum::PAYMENT_REFUND_FAILED => null /* @todo */,
            EventNameEnum::PAYMENT_REFUND_COMPLETED => RefundCompleted::fromJson($string),
            EventNameEnum::PAYMENT_CANCEL_CREATED => CancelCreated::fromJson($string),
            EventNameEnum::PAYMENT_CANCEL_FAILED => null /* @todo */,
        };
    }
}
