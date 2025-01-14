<?php

declare(strict_types=1);

namespace NexiCheckout\Http;

class Configuration
{
    public function __construct(
        protected string $secretKey,
        protected string $baseUrl,
        protected ?string $commercePlatformTag = null,
    ) {
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getCommercePlatformTag(): ?string
    {
        return $this->commercePlatformTag;
    }
}
