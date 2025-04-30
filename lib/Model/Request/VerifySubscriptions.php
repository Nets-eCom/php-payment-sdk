<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

use NexiCheckout\Model\Request\VerifySubscriptions\Subscription;

class VerifySubscriptions implements \JsonSerializable
{
    /**
     * @param list<Subscription> $subscriptions
     */
    public function __construct(
        private readonly array $subscriptions,
        private readonly ?string $externalBulkVerificationId = null
    ) {
    }

    /**
     * @return array{
     *     externalBulkVerificationId: string,
     *     subscriptions: Subscription[]
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'externalBulkVerificationId' => $this->externalBulkVerificationId,
            'subscriptions' => $this->subscriptions,
        ];
    }
}
