<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

class OrderItem
{
    public function __construct(
        private readonly string $reference,
        private readonly string $name,
        private readonly float|int $quantity,
        private readonly string $unit,
        private readonly int $unitPrice,
        private readonly int $taxRate,
        private readonly int $taxAmount,
        private readonly int $netTotalAmount,
        private readonly int $grossTotalAmount,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            reference: $data['reference'],
            name: $data['name'],
            quantity: $data['quantity'],
            unit: $data['unit'],
            unitPrice: $data['unitPrice'],
            taxRate: $data['taxRate'],
            taxAmount: $data['taxAmount'],
            netTotalAmount: $data['netTotalAmount'],
            grossTotalAmount: $data['grossTotalAmount'],
        );
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getQuantity(): float
    {
        return $this->quantity;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function getTaxRate(): int
    {
        return $this->taxRate;
    }

    public function getTaxAmount(): int
    {
        return $this->taxAmount;
    }

    public function getNetTotalAmount(): int
    {
        return $this->netTotalAmount;
    }

    public function getGrossTotalAmount(): int
    {
        return $this->grossTotalAmount;
    }
}
