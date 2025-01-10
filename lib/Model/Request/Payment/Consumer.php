<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

final class Consumer implements \JsonSerializable
{
    public function __construct(
        private readonly string $email,
        private readonly ?string $reference,
        private readonly ?Address $shippingAddress,
        private readonly ?Address $billingAddress,
        private readonly ?PhoneNumber $phoneNumber = null,
        private readonly ?PrivatePerson $privatePerson = null,
        private readonly ?Company $company = null,
    ) {
    }

    /**
     * @return array{
     *     email: string,
     *     reference: ?string,
     *     shippingAddress: ?Address,
     *     billingAddress: ?Address,
     *     phoneNumber: ?PhoneNumber,
     *     privatePerson: ?PrivatePerson,
     *     company: ?Company,
     * }
     */
    public function jsonSerialize(): mixed
    {
        return [
            'email' => $this->email,
            'reference' => $this->reference,
            'shippingAddress' => $this->shippingAddress,
            'billingAddress' => $this->billingAddress,
            'phoneNumber' => $this->phoneNumber,
            'privatePerson' => $this->privatePerson,
            'company' => $this->company,
        ];
    }
}
