<?php declare(strict_types=1);

namespace NexiCheckout\Model\Result\Payment;

use NexiCheckout\Model\Result\PaymentResult;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class PaymentWithHostedCheckoutResult extends PaymentResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        protected string $paymentId,
        private readonly string $hostedPaymentPageUrl
    ) {
        parent::__construct($paymentId);
    }

    public function getHostedPaymentPageUrl(): string
    {
        return $this->hostedPaymentPageUrl;
    }

    public static function fromJson(string $string): PaymentWithHostedCheckoutResult
    {
        return new self(...self::jsonDeserialize($string));
    }
}
