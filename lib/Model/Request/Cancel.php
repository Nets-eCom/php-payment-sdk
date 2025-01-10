<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

final class Cancel implements \JsonSerializable
{
    public function __construct(private readonly int $amount)
    {
    }

    /**
     * @return array{
     *     amount: int
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
        ];
    }
}
