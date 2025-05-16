<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data\Consumer;

class Address
{
    public function __construct(
        private readonly ?string $addressLine1,
        private readonly ?string $addressLine2,
        private readonly ?string $city,
        private readonly ?string $country,
        private readonly ?string $postcode,
        private readonly ?string $receiverLine
    ) {
    }

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function getReceiverLine(): ?string
    {
        return $this->receiverLine;
    }
}
