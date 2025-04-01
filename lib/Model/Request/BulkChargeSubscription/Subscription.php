<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\BulkChargeSubscription;

use NexiCheckout\Model\Request\Shared\Order;

class Subscription implements \JsonSerializable
{
    public function __construct(
        private readonly string $subscriptionId,
        private readonly string $externalReference,
        private readonly Order $order
    ) {
    }

    /**
     * @return array{
     *     subscriptionId: string,
     *     externalReference: string,
     *     order: Order
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'subscriptionId' => $this->subscriptionId,
            'externalReference' => $this->externalReference,
            'order' => $this->order,
        ];
    }
}
