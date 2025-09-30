<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

use NexiCheckout\Model\Request\Shared\Notification;
use NexiCheckout\Model\Request\Shared\Order;

class ChargeSubscription implements \JsonSerializable
{
    public function __construct(
        private readonly Order $order,
        private readonly ?Notification $notification,
    ) {
    }

    /**
     * @return array{
     *     order: Order,
     *     notifications: ?Notification,
     * }
     */
    public function jsonSerialize(): array
    {
        $result = [
            'order' => $this->order,
        ];

        if ($this->notification instanceof Notification) {
            $result['notifications'] = $this->notification;
        }

        return $result;
    }
}
