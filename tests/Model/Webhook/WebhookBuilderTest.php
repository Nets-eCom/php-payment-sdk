<?php

declare(strict_types=1);

namespace NexiCheckout\Tests\Model\Webhook;

use NexiCheckout\Model\Webhook\CancelCreated;
use NexiCheckout\Model\Webhook\ChargeCreated;
use NexiCheckout\Model\Webhook\CheckoutCompleted;
use NexiCheckout\Model\Webhook\PaymentCreated;
use NexiCheckout\Model\Webhook\RefundCompleted;
use NexiCheckout\Model\Webhook\ReservationCreated;
use NexiCheckout\Model\Webhook\ReservationCreatedV1;
use NexiCheckout\Model\Webhook\WebhookBuilder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class WebhookBuilderTest extends TestCase
{
    /**
     * @param array<string, mixed> $assertValues
     */
    #[DataProvider('providePayload')]
    public function testCreateFromJson(string $payload, string $eventName, string $dataClass, array $assertValues): void
    {
        $result = WebhookBuilder::fromJson($payload);

        $this->assertSame($eventName, $result->getEvent()->value);
        $this->assertInstanceOf($dataClass, $result);

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        foreach ($assertValues as $accessorStr => $expectedValue) {
            $this->assertSame($expectedValue, $propertyAccessor->getValue($result, $accessorStr));
        }

    }

    /**
     * @return iterable<array{string, string, string, array<string, mixed>}>
     */
    public static function providePayload(): iterable
    {
        yield [
            file_get_contents(__DIR__ . '/payloads/payment.charge.created.v2.json'),
            'payment.charge.created.v2',
            ChargeCreated::class,
            [
                'merchantNumber' => 100017120,
                'data.paymentId' => '025400006091b1ef6937598058c4e487',
                'data.chargeId' => '01ee00006091b2196937598058c4e488',
                'data.paymentMethod' => 'Visa',
                'data.amount.amount' => 5500,
                'data.amount.currency' => 'SEK',
            ],
        ];

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.refund.completed.json'),
            'payment.refund.completed',
            RefundCompleted::class,
            [
                'data.paymentId' => '025400006091b1ef6937598058c4e487',
            ],
        ];

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.cancel.created.json'),
            'payment.cancel.created',
            CancelCreated::class,
            [
                'data.paymentId' => '025400006091b1ef6937598058c4e487',
            ],
        ];

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.checkout.completed.json'),
            'payment.checkout.completed',
            CheckoutCompleted::class,
            [
                'data.paymentId' => '025400006091b1ef6937598058c4e487',
                'data.consumer.phoneNumber.number' => '12345678',
            ],
        ];

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.checkout.completed.missingData.json'),
            'payment.checkout.completed',
            CheckoutCompleted::class,
            [
                'data.paymentId' => '025400006091b1ef6937598058c4e487',
                'data.consumer.phoneNumber' => null,
            ],
        ];

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.reservation.created.v1.json'),
            'payment.reservation.created',
            ReservationCreatedV1::class,
            [
                'merchantId' => 100242833,
                'data.paymentId' => 'b015690c89d141f7b98b99dee769be62',
                'data.consumer.merchantReference' => '1234567890',
                'data.consumer.billingAddress.city' => 'Copenhagen',
                'data.cardDetails.truncatedPan' => '374500*****1009',
            ],
        ];

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.reservation.created.v2.json'),
            'payment.reservation.created.v2',
            ReservationCreated::class,
            [
                'data.paymentId' => 'b015690c89d141f7b98b99dee769be62',
            ],
        ];

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.created.json'),
            'payment.created',
            PaymentCreated::class,
            [
                'data.paymentId' => 'b015690c89d141f7b98b99dee769be62',
            ],
        ];
    }
}
