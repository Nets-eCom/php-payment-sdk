<?php

declare(strict_types=1);

namespace NexiCheckout\Tests\Model\Result;

use NexiCheckout\Model\Result\RetrievePaymentResult;
use PHPUnit\Framework\TestCase;

class RetrievePaymentResultTest extends TestCase
{
    public function testItCanInstantiateFromJsonString(): void
    {
        $this->assertInstanceOf(
            RetrievePaymentResult::class,
            RetrievePaymentResult::fromJson($this->getReservedPaymentResult())
        );
    }

    public function testExpiresAtIsParsedFromJson(): void
    {
        $json = <<<JSON
        {
            "payment": {
                "paymentId": "025400006091b1ef6937598058c4e487",
                "consumer": {
                    "shippingAddress": {},
                    "billingAddress": {},
                    "privatePerson": {},
                    "company": {}
                },
                "orderDetails": {
                    "amount": 100,
                    "currency": "EUR"
                },
                "checkout": {
                    "url": "https://example.com/checkout"
                },
                "created": "2019-08-24T14:15:22Z",
                "summary": {
                    "reservedAmount": 0,
                    "reservedSurchargeAmount": 0
                },
                "expiresAt": "2026-10-08T14:15:22Z"
            }
        }
        JSON;

        $result = RetrievePaymentResult::fromJson($json);

        $this->assertEquals(new \DateTimeImmutable('2026-10-08T14:15:22Z'), $result->getPayment()->getExpiresAt());
    }

    public function testExpiresAtIsNullWhenAbsentFromJson(): void
    {
        $result = RetrievePaymentResult::fromJson($this->getReservedPaymentResult());

        $this->assertNull($result->getPayment()->getExpiresAt());
    }

    private function getReservedPaymentResult(): string
    {
        return <<<JSON
        {
            "payment": {
                "paymentId": "025400006091b1ef6937598058c4e487",
                "summary": {
                    "reservedAmount": 100,
                    "reservedSurchargeAmount": 0
                },
                "consumer": {
                    "shippingAddress": {},
                    "billingAddress": {},
                    "privatePerson": {},
                    "company": {}
                },
                "paymentDetails": {},
                "orderDetails": {
                    "amount": 100,
                    "currency": "EUR"
                },
                "checkout": {
                    "url": "https://example.com/checkout",
                    "cancelUrl": null
                },
                "created": "2019-08-24T14:15:22Z",
                "refunds": [],
                "charges": [],
                "subscription": {
                  "id": "foo"
                },
                "unscheduledSubscription": {
                  "unscheduledSubscriptionId": "foo"
                }
            }
        }
JSON;
    }
}
