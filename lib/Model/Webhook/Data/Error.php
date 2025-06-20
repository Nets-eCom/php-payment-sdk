<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

class Error
{
    public function __construct(
        private readonly string $code,
        private readonly string $message,
        private readonly string $source,
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getSource(): string
    {
        return $this->source;
    }
}
