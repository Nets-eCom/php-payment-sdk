<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\UpdateOrder;

class Shipping implements \JsonSerializable
{
    public function __construct(private readonly bool $costSpecified)
    {
    }

    /**
     * @return array{
     *     "costSpecified": bool
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'costSpecified' => $this->costSpecified,
        ];
    }
}
