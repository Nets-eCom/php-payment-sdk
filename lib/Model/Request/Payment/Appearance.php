<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

final class Appearance implements \JsonSerializable
{
    /**
     * @return array<int, mixed>
     */
    public function jsonSerialize(): array
    {
        return [];
    }
}
