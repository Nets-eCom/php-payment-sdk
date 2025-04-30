<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\VerifySubscriptions;

class Subscription implements \JsonSerializable
{
    public function __construct(
        private readonly ?string $subscriptionId = null,
        private readonly ?string $externalReference = null
    ) {
    }

    /**
     * @return array{
     *     subscriptionId: string,
     *     externalReference: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'subscriptionId' => $this->subscriptionId,
            'externalReference' => $this->externalReference,
        ];
    }
}
