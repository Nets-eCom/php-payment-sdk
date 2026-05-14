<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Result\RetrieveSubscription\PaymentDetails;
use NexiCheckout\Model\Result\Shared\CardDetails;
use NexiCheckout\Model\Result\Shared\PaymentTypeEnum;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class RetrieveUnscheduledSubscriptionResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly string $unscheduledSubscriptionId,
        private readonly PaymentDetails $paymentDetails,
    ) {
    }

    public function getUnscheduledSubscriptionId(): string
    {
        return $this->unscheduledSubscriptionId;
    }

    public function getPaymentDetails(): PaymentDetails
    {
        return $this->paymentDetails;
    }

    public static function fromJson(string $string): RetrieveUnscheduledSubscriptionResult
    {
        $data = self::jsonDeserialize($string);

        return new RetrieveUnscheduledSubscriptionResult(
            $data['unscheduledSubscriptionId'],
            new PaymentDetails(
                PaymentTypeEnum::tryFrom($data['paymentDetails']['paymentType']),
                $data['paymentDetails']['paymentMethod'],
                CardDetails::fromArray($data['paymentDetails']['cardDetails']),
            ),
        );
    }
}
