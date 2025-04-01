<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Shared\Notification;

class Header
{
    public function __construct(private readonly string $name, private readonly string $value)
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
