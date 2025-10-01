<?php

declare(strict_types=1);

namespace NexiCheckout\Http\Header;

final class IdempotencyKey implements HeaderOption
{
    public function __construct(private readonly string $value)
    {
        $len = \strlen($value);
        if ($len < 1 || $len > 64) {
            throw new \InvalidArgumentException('Idempotency-Key must be 1..64 characters long.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function headerName(): string
    {
        return 'Idempotency-Key';
    }

    public function headerValue(): string
    {
        return $this->value;
    }
}
