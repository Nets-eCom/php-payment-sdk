<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

use NexiCheckout\Model\Request\Payment\Checkout;
use NexiCheckout\Model\Request\Payment\Notification;
use NexiCheckout\Model\Request\Payment\Order;

class Payment implements \JsonSerializable
{
    public function __construct(
        private readonly Order $order,
        private readonly Checkout $checkout,
        private readonly ?Notification $notification = null,
        private readonly ?string $merchantNumber = null,
        private readonly ?string $myReference = null
    ) {
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getCheckout(): Checkout
    {
        return $this->checkout;
    }

    public function getNotification(): ?Notification
    {
        return $this->notification;
    }

    public function getMerchantNumber(): ?string
    {
        return $this->merchantNumber;
    }

    public function getMyReference(): ?string
    {
        return $this->myReference;
    }

    /**
     * @return array{
     *     order: Order,
     *     checkout: Checkout,
     *     notifications: ?Notification,
     *     merchantNumber: ?string,
     *     myReference:  ?string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'order' => $this->order,
            'checkout' => $this->checkout,
            'notifications' => $this->notification,
            'merchantNumber' => $this->merchantNumber,
            'myReference' => $this->myReference,
        ];
    }
}
