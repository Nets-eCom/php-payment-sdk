<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

class PrivatePerson implements \JsonSerializable
{
    public function __construct(
        private readonly string $firstName,
        private readonly string $lastName
    ) {
    }

    /**
     * @return array{
     *     firstName: string,
     *     lastName: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }
}
