<?php

declare(strict_types=1);

namespace NexiCheckout\Tests\Model\Webhook;

use NexiCheckout\Model\Webhook\CancelCreated;
use NexiCheckout\Model\Webhook\CancelFailed;
use NexiCheckout\Model\Webhook\ChargeCreated;
use NexiCheckout\Model\Webhook\ChargeFailed;
use NexiCheckout\Model\Webhook\CheckoutCompleted;
use NexiCheckout\Model\Webhook\PaymentCreated;
use NexiCheckout\Model\Webhook\RefundCompleted;
use NexiCheckout\Model\Webhook\RefundFailed;
use NexiCheckout\Model\Webhook\RefundInitiated;
use NexiCheckout\Model\Webhook\ReservationCreated;
use NexiCheckout\Model\Webhook\ReservationCreatedV1;
use NexiCheckout\Model\Webhook\ReservationFailed;
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
            file_get_contents(__DIR__ . '/payloads/payment.charge.failed.json'),
            'payment.charge.failed',
            ChargeFailed::class,
            [
                'merchantId' => 100242833,
                'data.paymentId' => '74d6fcdd11e2486c987d526250d1258f',
                'data.error.code' => '911',
                'data.error.message' => 'Error occurred',
                'data.error.source' => 'Internal',
                'data.chargeId' => 'e9f346328d684bf59f6cac006dbb2ec4',
                'data.reconciliationReference' => 'MRJhJvEDCx1y7uWlKfb6O3z78',
                'data.amount.amount' => 10000,
                'data.amount.currency' => 'SEK',
                'data.invoiceDetails.accountNumber' => '1234567890',
                'data.invoiceDetails.distributionType' => 'Email',
                'data.invoiceDetails.invoiceNumber' => '1234567890',
                'data.invoiceDetails.ocrOrkid' => '1234567890',
                'data.invoiceDetails.ourReference' => '1234567890',
                'data.invoiceDetails.yourReference' => '9876543210',
            ],
        ];

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.refund.completed.json'),
            'payment.refund.completed',
            RefundCompleted::class,
            [
                'merchantId' => 100242833,
                'data.amount.amount' => 5500,
                'data.amount.currency' => 'SEK',
                'data.paymentId' => '025400006091b1ef6937598058c4e487',
                'data.invoiceDetails.accountNumber' => '1234567890',
                'data.invoiceDetails.distributionType' => 'Email',
                'data.invoiceDetails.invoiceNumber' => '1234567890',
                'data.invoiceDetails.ocrOrkid' => '1234567890',
                'data.invoiceDetails.ourReference' => '1234567890',
                'data.invoiceDetails.yourReference' => '9876543210',
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
            file_get_contents(__DIR__ . '/payloads/payment.reservation.failed.json'),
            'payment.reservation.failed',
            ReservationFailed::class,
            [
                'data.paymentId' => 'b015690c89d141f7b98b99dee769be62',
                'data.error.code' => '911',
                'data.amount.amount' => 10000,
            ],
        ];

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.refund.initiated.v2.json'),
            'payment.refund.initiated.v2',
            RefundInitiated::class,
            [
                'merchantId' => 100017120,
                'data.paymentId' => '025400006091b1ef6937598058c4e487',
                'data.reconciliationReference' => '12345',
                'data.paymentActionId' => '12345',
                'data.amount.amount' => 5500,
                'data.amount.currency' => 'SEK',
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

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.refund.failed.json'),
            'payment.refund.failed',
            RefundFailed::class,
            [
                'merchantId' => 100242833,
                'data.paymentId' => 'b015690c89d141f7b98b99dee769be62',
                'data.error.code' => '911',
                'data.error.message' => 'Error occurred',
                'data.error.source' => 'Internal',
                'data.refundId' => '32e1cb8de6704c4baf9974121cc1351f',
                'data.reconciliationReference' => 'MRJhJvEDCx1y7uWlKfb6O3z78',
                'data.amount.amount' => 10000,
                'data.amount.currency' => 'SEK',
                'data.invoiceDetails.accountNumber' => '1234567890',
                'data.invoiceDetails.distributionType' => 'Email',
                'data.invoiceDetails.invoiceNumber' => '1234567890',
                'data.invoiceDetails.ocrOrkid' => '1234567890',
                'data.invoiceDetails.ourReference' => '1234567890',
                'data.invoiceDetails.yourReference' => '9876543210',
            ],
        ];

        yield [
            file_get_contents(__DIR__ . '/payloads/payment.cancel.failed.json'),
            'payment.cancel.failed',
            CancelFailed::class,
            [
                'merchantId' => 100242833,
                'data.paymentId' => 'b015690c89d141f7b98b99dee769be62',
                'data.error.code' => '911',
                'data.error.message' => 'Error occurred',
                'data.error.source' => 'Internal',
                'data.cancelId' => '1234',
                'data.amount.amount' => 10000,
                'data.amount.currency' => 'SEK',
            ],
        ];
    }
}
