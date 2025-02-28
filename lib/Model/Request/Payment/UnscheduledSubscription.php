<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

class UnscheduledSubscription implements \JsonSerializable
{
    public function __construct(
        private readonly ?bool $create = null,
        private readonly ?string $unscheduledSubscriptionId = null
    ) {
    }

    /**
     * @return array{
     *     "create": ?bool,
     *     "unscheduledSubscriptionId": ?string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'create' => $this->create,
            'unscheduledSubscriptionId' => $this->unscheduledSubscriptionId,
        ];
    }
}
