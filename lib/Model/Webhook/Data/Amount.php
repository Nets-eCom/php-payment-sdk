<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

class Amount
{
    public function __construct(
        private readonly int $amount,
        private readonly string $currency,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            amount: $data['amount'],
            currency: $data['currency'],
        );
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }
}
