<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment\Consumer;

use NexiCheckout\Model\Result\Shared\PhoneNumber;

class Address
{
    public function __construct(
        private readonly ?string $addressLine1,
        private readonly ?string $addressLine2,
        private readonly ?string $receiverLine,
        private readonly ?string $postalCode,
        private readonly ?string $city,
        private readonly ?PhoneNumber $phoneNumber = null
    ) {
    }

    public function getAddressLine1(): string
    {
        return $this->addressLine1;
    }

    public function getAddressLine2(): string
    {
        return $this->addressLine2;
    }

    public function getReceiverLine(): string
    {
        return $this->receiverLine;
    }

    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }
}
