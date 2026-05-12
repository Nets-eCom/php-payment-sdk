<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

use NexiCheckout\Model\Request\CardPayment\CardDetails;
use NexiCheckout\Model\Request\CardPayment\InteractionType;
use NexiCheckout\Model\Request\CardPayment\PaymentInfo;
use NexiCheckout\Model\Request\Shared\Notification;
use NexiCheckout\Model\Request\Shared\Order;

final class CardPayment implements \JsonSerializable
{
    public function __construct(
        private readonly Order $order,
        private readonly PaymentInfo $paymentInfo,
        private readonly CardDetails $cardDetails,
        private readonly ?InteractionType $interactionType = null,
        private readonly ?string $merchantNumber = null,
        private readonly ?Notification $notification = null,
        private readonly ?string $myReference = null,
    ) {
    }

    /**
     * @return array{
     *     interactionType: int|null,
     *     order: Order,
     *     paymentInfo: PaymentInfo,
     *     merchantNumber: ?string,
     *     notifications: ?Notification,
     *     myReference: ?string,
     *     cardDetails: CardDetails,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'interactionType' => $this->interactionType?->value,
            'order' => $this->order,
            'paymentInfo' => $this->paymentInfo,
            'merchantNumber' => $this->merchantNumber,
            'notifications' => $this->notification,
            'myReference' => $this->myReference,
            'cardDetails' => $this->cardDetails,
        ];
    }
}
