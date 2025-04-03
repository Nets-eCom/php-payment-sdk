<?php

declare(strict_types=1);

namespace NexiCheckout\Tests\Model\Result;

use NexiCheckout\Model\Result\RetrievePayment\PaymentStatusEnum;
use NexiCheckout\Model\Result\RetrievePaymentResult;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RetrievePaymentResultStatusTest extends TestCase
{
    #[DataProvider('paymentStatusProvider')]
    public function testPaymentStatus(string $payload, PaymentStatusEnum $expectedStatus): void
    {
        $retrievePaymentResult = RetrievePaymentResult::fromJson($payload);

        $this->assertSame($expectedStatus, $retrievePaymentResult->getPayment()->getStatus());
    }

    /**
     * @return array<string, array{string, PaymentStatusEnum}>
     */
    public static function paymentStatusProvider(): array
    {
        return [
            'payment is reserved' => [
                <<<JSON
                {
                    "payment": {
                        "paymentId": "025400006091b1ef6937598058c4e487",
                        "summary": {
                            "reservedAmount": 100
                        },
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
                            "url": "https://example.com/checkout",
                            "cancelUrl": null
                        },
                        "created": "2019-08-24T14:15:22Z"
                    }
                }
                JSON,
                PaymentStatusEnum::RESERVED,
            ],
            'payment is charged' => [
                <<<JSON
                {
                    "payment": {
                        "paymentId": "025400006091b1ef6937598058c4e487",
                        "summary": {
                            "reservedAmount": 100,
                            "chargedAmount": 100
                        },
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
                            "url": "https://example.com/checkout",
                            "cancelUrl": null
                        },
                        "created": "2019-08-24T14:15:22Z"
                    }
                }
                JSON,
                PaymentStatusEnum::CHARGED,
            ],
            'payment is charged but without reservation' => [
                <<<JSON
                {
                    "payment": {
                        "paymentId": "025400006091b1ef6937598058c4e487",
                        "summary": {
                            "chargedAmount": 100
                        },
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
                            "url": "https://example.com/checkout",
                            "cancelUrl": null
                        },
                        "created": "2019-08-24T14:15:22Z"
                    }
                }
                JSON,
                PaymentStatusEnum::CHARGED,
            ],
            'payment is refunded' => [
                <<<JSON
                {
                    "payment": {
                        "paymentId": "025400006091b1ef6937598058c4e487",
                        "summary": {
                            "reservedAmount": 100,
                            "chargedAmount": 100,
                            "refundedAmount": 100
                        },
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
                            "url": "https://example.com/checkout",
                            "cancelUrl": null
                        },
                        "created": "2019-08-24T14:15:22Z"
                    }
                }
                JSON,
                PaymentStatusEnum::REFUNDED,
            ],
            'payment is cancelled' => [
                <<<JSON
                {
                    "payment": {
                        "paymentId": "025400006091b1ef6937598058c4e487",
                        "summary": {
                            "reservedAmount": 100,
                            "chargedAmount": 100,
                            "refundedAmount": 0,
                            "cancelledAmount": 100
                        },
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
                            "url": "https://example.com/checkout",
                            "cancelUrl": null
                        },
                        "created": "2019-08-24T14:15:22Z"
                    }
                }
                JSON,
                PaymentStatusEnum::CANCELLED,
            ],
            'payment is parially charged' => [
                <<<JSON
                {
                    "payment": {
                        "paymentId": "025400006091b1ef6937598058c4e487",
                        "summary": {
                            "reservedAmount": 100,
                            "chargedAmount": 80,
                            "refundedAmount": 0,
                            "cancelledAmount": 0
                        },
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
                            "url": "https://example.com/checkout",
                            "cancelUrl": null
                        },
                        "created": "2019-08-24T14:15:22Z"
                    }
                }
                JSON,
                PaymentStatusEnum::PARTIALLY_CHARGED,
            ],
            'payment is parially cancelled' => [
                <<<JSON
                {
                    "payment": {
                        "paymentId": "025400006091b1ef6937598058c4e487",
                        "summary": {
                            "reservedAmount": 100,
                            "chargedAmount": 80,
                            "refundedAmount": 0,
                            "cancelledAmount": 20
                        },
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
                            "url": "https://example.com/checkout",
                            "cancelUrl": null
                        },
                        "created": "2019-08-24T14:15:22Z"
                    }
                }
                JSON,
                PaymentStatusEnum::PARTIALLY_CHARGED,
            ],
            'payment is new' => [
                <<<JSON
                {
                    "payment": {
                        "paymentId": "025400006091b1ef6937598058c4e487",
                        "summary": {},
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
                            "url": "https://example.com/checkout",
                            "cancelUrl": null
                        },
                        "created": "2019-08-24T14:15:22Z"
                    }
                }
                JSON,
                PaymentStatusEnum::NEW,
            ],
        ];
    }
}
