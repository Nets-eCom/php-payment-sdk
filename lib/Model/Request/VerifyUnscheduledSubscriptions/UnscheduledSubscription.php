<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\VerifyUnscheduledSubscriptions;

class UnscheduledSubscription implements \JsonSerializable
{
    public function __construct(
        private readonly ?string $unscheduledSubscriptionId = null,
        private readonly ?string $externalReference = null
    ) {
    }

    /**
     * @return array{
     *     unscheduledSubscriptionId: string,
     *     externalReference: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'unscheduledSubscriptionId' => $this->unscheduledSubscriptionId,
            'externalReference' => $this->externalReference,
        ];
    }
}
