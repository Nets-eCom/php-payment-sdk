<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

class Address implements \JsonSerializable
{
    public function __construct(
        private readonly string $addressLine1,
        private readonly ?string $addressLine2,
        private readonly string $postalCode,
        private readonly string $city,
        private readonly string $country,
    ) {
    }

    /**
     * @return array{
     *     addressLine1: string,
     *     addressLine2?: ?string,
     *     postalCode: string,
     *     city: string,
     *     country: string,
     * }
     */
    public function jsonSerialize(): array
    {
        $result = [
            'addressLine1' => $this->addressLine1,
            'addressLine2' => $this->addressLine2,
            'city' => $this->city,
            'country' => $this->country,
        ];

        $filtered = array_filter($result);
        $filtered['postalCode'] = $this->postalCode;

        return $filtered;
    }
}
