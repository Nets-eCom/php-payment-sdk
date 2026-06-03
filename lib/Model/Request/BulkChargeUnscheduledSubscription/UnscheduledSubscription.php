<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\BulkChargeUnscheduledSubscription;

use NexiCheckout\Model\Request\Shared\Order;

class UnscheduledSubscription implements \JsonSerializable
{
    public function __construct(
        private readonly Order $order,
        private readonly ?string $unscheduledSubscriptionId = null,
        private readonly ?string $externalReference = null,
        private readonly ?string $myReference = null,
    ) {
    }

    /**
     * @return array{
     *     unscheduledSubscriptionId?: string,
     *     externalReference?: string,
     *     order: Order,
     *     myReference?: string
     * }
     */
    public function jsonSerialize(): array
    {
        $result = [
            'order' => $this->order,
        ];

        if ($this->unscheduledSubscriptionId !== null) {
            $result['unscheduledSubscriptionId'] = $this->unscheduledSubscriptionId;
        }

        if ($this->externalReference !== null) {
            $result['externalReference'] = $this->externalReference;
        }

        if ($this->myReference !== null) {
            $result['myReference'] = $this->myReference;
        }

        return $result;
    }
}
