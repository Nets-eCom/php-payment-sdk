<?php declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment\Consumer;

use NexiCheckout\Model\Result\RetrievePayment\Company\ContactDetails;

class Company
{
    public function __construct(
        private readonly ?string $merchantReference,
        private readonly ?string $name,
        private readonly ?string $registrationNumber,
        private readonly ?ContactDetails $contactDetails,
    ) {
    }

    public function getMerchantReference(): ?string
    {
        return $this->merchantReference;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getRegistrationNumber(): ?string
    {
        return $this->registrationNumber;
    }

    public function getContactDetails(): ?ContactDetails
    {
        return $this->contactDetails;
    }
}
