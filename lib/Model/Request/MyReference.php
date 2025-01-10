<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

final class MyReference implements \JsonSerializable
{
    public function __construct(
        private readonly string $myReference
    ) {
    }

    /**
     * @return array{
     *     myReference: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'myReference' => $this->myReference,
        ];
    }
}
