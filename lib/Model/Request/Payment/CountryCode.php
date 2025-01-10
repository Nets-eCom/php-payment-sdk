<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

final class CountryCode implements \JsonSerializable
{
    public function __construct(private readonly string $code)
    {
    }

    /**
     * @return array{countryCode: string}
     */
    public function jsonSerialize(): array
    {
        return [
            'countryCode' => $this->code,
        ];
    }
}
