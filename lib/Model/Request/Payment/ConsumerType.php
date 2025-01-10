<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

final class ConsumerType implements \JsonSerializable
{
    /**
     * @param list<string> $supportedTypes
     */
    public function __construct(
        private readonly string $default,
        private readonly array $supportedTypes
    ) {
    }

    /**
     * @return array{
     *     default: string,
     *     supportedTypes: list<string>
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'default' => $this->default,
            'supportedTypes' => $this->supportedTypes,
        ];
    }
}
