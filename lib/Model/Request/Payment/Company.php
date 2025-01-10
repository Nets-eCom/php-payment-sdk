<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

class Company implements \JsonSerializable
{
    public function __construct(
        private readonly string $name,
        private readonly string $firstName,
        private readonly string $lastName
    ) {
    }

    /**
     * @return array{
     *     name: string,
     *     contact: array{
     *         firstName: string,
     *         lastName: string
     *     }
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'name' => $this->name,
            'contact' => [
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
            ],
        ];
    }
}
