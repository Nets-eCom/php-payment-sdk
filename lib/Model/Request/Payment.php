<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

use NexiCheckout\Model\Request\Payment\Checkout;
use NexiCheckout\Model\Request\Payment\MethodConfiguration;
use NexiCheckout\Model\Request\Payment\Subscription;
use NexiCheckout\Model\Request\Payment\UnscheduledSubscription;
use NexiCheckout\Model\Request\Shared\Notification;
use NexiCheckout\Model\Request\Shared\Order;

class Payment implements \JsonSerializable
{
    /**
     * @param list<MethodConfiguration> $paymentMethodsConfiguration
     */
    public function __construct(
        private readonly Order $order,
        private readonly Checkout $checkout,
        private readonly ?Notification $notification = null,
        private readonly ?Subscription $subscription = null,
        private readonly ?UnscheduledSubscription $unscheduledSubscription = null,
        private readonly ?string $merchantNumber = null,
        private readonly ?string $myReference = null,
        private readonly array $paymentMethodsConfiguration = []
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
     * @return list<MethodConfiguration>
     */
    public function getPaymentMethodsConfiguration(): array
    {
        return $this->paymentMethodsConfiguration;
    }

    /**
     * @return array{
     *     order: Order,
     *     checkout: Checkout,
     *     notifications: ?Notification,
     *     merchantNumber: ?string,
     *     myReference:  ?string,
     *     paymentMethodsConfiguration: list<MethodConfiguration>
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'order' => $this->order,
            'checkout' => $this->checkout,
            'notifications' => $this->notification,
            'subscription' => $this->subscription,
            'unscheduledSubscription' => $this->unscheduledSubscription,
            'merchantNumber' => $this->merchantNumber,
            'myReference' => $this->myReference,
            'paymentMethodsConfiguration' => $this->paymentMethodsConfiguration,
        ];
    }
}
