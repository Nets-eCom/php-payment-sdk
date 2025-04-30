<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

use NexiCheckout\Model\Request\BulkChargeSubscription\Subscription;
use NexiCheckout\Model\Request\Shared\Notification;

class BulkChargeSubscription implements \JsonSerializable
{
    /**
     * @param list<Subscription> $subscriptions
     */
    public function __construct(
        private readonly string $externalBulkChargeId,
        private readonly Notification $notification,
        private readonly array $subscriptions
    ) {
    }

    /**
     * @return array{
     *     externalBulkChargeId: string,
     *     notifications: Notification,
     *     subscriptions: Subscription[]
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'externalBulkChargeId' => $this->externalBulkChargeId,
            'notifications' => $this->notification,
            'subscriptions' => $this->subscriptions,
        ];
    }
}
