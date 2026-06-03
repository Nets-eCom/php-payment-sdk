<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

use NexiCheckout\Model\Request\BulkChargeUnscheduledSubscription\UnscheduledSubscription;
use NexiCheckout\Model\Request\Shared\Notification;

class BulkChargeUnscheduledSubscription implements \JsonSerializable
{
    /**
     * @param list<UnscheduledSubscription> $unscheduledSubscriptions
     */
    public function __construct(
        private readonly array $unscheduledSubscriptions,
        private readonly ?string $externalBulkChargeId = null,
        private readonly ?Notification $notification = null,
    ) {
    }

    /**
     * @return array{
     *     externalBulkChargeId?: string,
     *     notifications?: Notification,
     *     unscheduledSubscriptions: UnscheduledSubscription[]
     * }
     */
    public function jsonSerialize(): array
    {
        $result = [
            'unscheduledSubscriptions' => $this->unscheduledSubscriptions,
        ];

        if ($this->externalBulkChargeId !== null) {
            $result['externalBulkChargeId'] = $this->externalBulkChargeId;
        }

        if ($this->notification instanceof Notification) {
            $result['notifications'] = $this->notification;
        }

        return $result;
    }
}
