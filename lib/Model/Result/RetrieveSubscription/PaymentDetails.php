<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrieveSubscription;

use NexiCheckout\Model\Result\Shared\CardDetails;
use NexiCheckout\Model\Result\Shared\PaymentTypeEnum;

class PaymentDetails
{
    public function __construct(
        private readonly PaymentTypeEnum $paymentType,
        private readonly string $paymentMethod,
        private readonly CardDetails $cardDetails,
    ) {
    }

    public function getPaymentType(): PaymentTypeEnum
    {
        return $this->paymentType;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getCardDetails(): CardDetails
    {
        return $this->cardDetails;
    }
}
