<?php

declare(strict_types=1);

namespace NexiCheckout\Tests\Model\Result;

use NexiCheckout\Model\Result\Payment\PaymentWithHostedCheckoutResult;
use PHPUnit\Framework\TestCase;

class PaymentWithHostedCheckoutResultTest extends TestCase
{
    public function testCreatesResultFromValidJsonWithExtraField(): void
    {
        $result = PaymentWithHostedCheckoutResult::fromJson(<<<JSON
        {
            "paymentId": "025400006091b1ef6937598058c4e487",
            "hostedPaymentPageUrl": "https://example.com/hosted-payment/123",
            "extraField": "extraValue"
        }
        JSON);

        $this->assertSame('025400006091b1ef6937598058c4e487', $result->getPaymentId());
        $this->assertSame('https://example.com/hosted-payment/123', $result->getHostedPaymentPageUrl());
    }
}
