<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Data\Consumer\Address;
use NexiCheckout\Model\Webhook\Data\Consumer\PhoneNumber;

class Consumer
{
    public function __construct(
        private readonly string $firstName,
        private readonly string $lastName,
        private readonly Address $billingAddress,
        private readonly string $country,
        private readonly string $email,
        private readonly string $ip,
        private readonly PhoneNumber $phoneNumber,
        private readonly Address $shippingAddress
    ) {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getBillingAddress(): Address
    {
        return $this->billingAddress;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getShippingAddress(): Address
    {
        return $this->shippingAddress;
    }
}
