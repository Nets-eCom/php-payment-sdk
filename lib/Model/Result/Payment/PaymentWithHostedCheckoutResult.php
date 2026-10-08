<?php declare(strict_types=1);

namespace NexiCheckout\Model\Result\Payment;

use NexiCheckout\Model\Result\PaymentResult;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

final class PaymentWithHostedCheckoutResult extends PaymentResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    private readonly ?\DateTimeInterface $expiresAt;

    public function __construct(
        protected string $paymentId,
        private readonly string $hostedPaymentPageUrl,
        ?\DateTimeInterface $expiresAt = null,
    ) {
        $this->expiresAt = $expiresAt;
        parent::__construct($paymentId);
    }

    public function getHostedPaymentPageUrl(): string
    {
        return $this->hostedPaymentPageUrl;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public static function fromJson(string $string): PaymentWithHostedCheckoutResult
    {
        $vars = self::jsonDeserializeToClassVars($string);

        if (isset($vars['expiresAt'])) {
            $vars['expiresAt'] = new \DateTimeImmutable($vars['expiresAt']);
        }

        return new self(...$vars);
    }
}
