<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\Payment;

use NexiCheckout\Model\Result\PaymentResult;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class PaymentWithEmbeddedCheckoutResult extends PaymentResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    private readonly ?\DateTimeInterface $expiresAt;

    public function __construct(
        protected string $paymentId,
        ?\DateTimeInterface $expiresAt = null,
    ) {
        $this->expiresAt = $expiresAt;
        parent::__construct($paymentId);
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public static function fromJson(string $string): PaymentWithEmbeddedCheckoutResult
    {
        $vars = self::jsonDeserializeToClassVars($string);

        if (isset($vars['expiresAt'])) {
            $vars['expiresAt'] = new \DateTimeImmutable($vars['expiresAt']);
        }

        return new self(...$vars);
    }
}
