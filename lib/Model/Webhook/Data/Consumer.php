<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Data\Consumer\Address;
use NexiCheckout\Model\Webhook\Data\Consumer\PhoneNumber;

class Consumer
{
    public function __construct(
        private readonly string $email,
        private readonly string $ip,
        private readonly string $country,
        private readonly Address $billingAddress,
        private readonly Address $shippingAddress,
        private readonly ?string $firstName = null,
        private readonly ?string $lastName = null,
        private readonly ?PhoneNumber $phoneNumber = null,
        private readonly ?string $merchantReference = null,
    ) {
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getBillingAddress(): Address
    {
        return $this->billingAddress;
    }

    public function getShippingAddress(): Address
    {
        return $this->shippingAddress;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getMerchantReference(): ?string
    {
        return $this->merchantReference;
    }
}
