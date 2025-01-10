<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

final class ReferenceInformation implements \JsonSerializable
{
    public function __construct(
        private readonly string $checkoutUrl,
        private readonly string $reference
    ) {
    }

    /**
     * @return array{
     *     checkoutUrl: string,
     *     reference: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'checkoutUrl' => $this->checkoutUrl,
            'reference' => $this->reference,
        ];
    }
}
