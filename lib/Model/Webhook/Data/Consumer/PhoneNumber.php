<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data\Consumer;

class PhoneNumber
{
    public function __construct(private readonly string $prefix, private readonly string $number)
    {
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getNumber(): string
    {
        return $this->number;
    }
}
