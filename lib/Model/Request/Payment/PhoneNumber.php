<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

class PhoneNumber implements \JsonSerializable
{
    public function __construct(
        private readonly string $prefix,
        private readonly string $number
    ) {
    }

    /**
     * @return array{
     *     prefix: string,
     *     number: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'prefix' => $this->prefix,
            'number' => $this->number,
        ];
    }
}
