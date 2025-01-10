<?php declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment\Company;

use NexiCheckout\Model\Result\Shared\PhoneNumber;

class ContactDetails
{
    public function __construct(
        private readonly ?string $firstName,
        private readonly ?string $lastName,
        private readonly ?string $email,
        private readonly ?PhoneNumber $phoneNumber
    ) {
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }
}
