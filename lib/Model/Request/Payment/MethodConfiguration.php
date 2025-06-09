<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

class MethodConfiguration implements \JsonSerializable
{
    public function __construct(
        private readonly string $name,
        private readonly bool $enabled
    ) {
    }

    /**
     * @return array{
     *     name: string,
     *     enabled: bool,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'enabled' => $this->enabled,
        ];
    }
}
