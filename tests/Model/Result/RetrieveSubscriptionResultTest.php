<?php

declare(strict_types=1);

namespace Model\Result;

use NexiCheckout\Model\Result\RetrieveSubscriptionResult;
use PHPUnit\Framework\TestCase;

class RetrieveSubscriptionResultTest extends TestCase
{
    public function testItCanInstantiateFromJsonString(): void
    {
        $this->assertInstanceOf(
            RetrieveSubscriptionResult::class,
            RetrieveSubscriptionResult::fromJson($this->getSubscriptionResult())
        );
    }

    private function getSubscriptionResult(): string
    {
        return <<<JSON
        {
            "subscriptionId": "d079718b-ff63-45dd-947b-4950c023750f",
            "frequency": 0,
            "interval": 0,
            "endDate": "2019-08-24T14:15:22Z",
            "paymentDetails": {
                "paymentType": "CARD",
                "paymentMethod": "Visa",
                "cardDetails": {
                    "expiryDate": "string",
                    "maskedPan": "string"
                }
            },
            "importError": {
                "importStepsResponseCode": "string",
                "importStepsResponseSource": "string",
                "importStepsResponseText": "string"
            }
        }
JSON;
    }
}
