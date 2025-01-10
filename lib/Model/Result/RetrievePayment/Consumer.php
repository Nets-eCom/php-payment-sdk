<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment;

use NexiCheckout\Model\Result\RetrievePayment\Consumer\Address;
use NexiCheckout\Model\Result\RetrievePayment\Consumer\Company;
use NexiCheckout\Model\Result\RetrievePayment\Consumer\PrivatePerson;

class Consumer
{
    public function __construct(
        private readonly Address $shippingAddress,
        private readonly Address $billingAddress,
        private readonly PrivatePerson $privatePerson,
        private readonly Company $company,
    ) {
    }

    public function getShippingAddress(): Address
    {
        return $this->shippingAddress;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getPrivatePerson(): PrivatePerson
    {
        return $this->privatePerson;
    }

    public function getBillingAddress(): Address
    {
        return $this->billingAddress;
    }
}
