<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\CardPayment;

use NexiCheckout\Model\Request\Shared\Consumer;

final class PaymentInfo implements \JsonSerializable
{
    public function __construct(
        private readonly ?bool $charge = null,
        private readonly ?Consumer $consumer = null,
    ) {
    }

    /**
     * @return array{
     *     consumer: ?Consumer,
     *     charge: ?bool,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'consumer' => $this->consumer,
            'charge' => $this->charge,
        ];
    }
}
