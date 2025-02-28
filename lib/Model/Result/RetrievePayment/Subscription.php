<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment;

class Subscription
{
    public function __construct(private readonly string $id)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
