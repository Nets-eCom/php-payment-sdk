<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

class Subscription implements \JsonSerializable
{
    public function __construct(
        private readonly ?string $subscriptionId = null,
        private readonly ?\DateTimeInterface $endDate = null,
        private readonly ?int $interval = null,
    ) {
    }

    /**
     * @return array{
     *     "subscriptionId": ?string,
     *     "endDate": ?string,
     *     "interval": ?int,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'subscriptionId' => $this->subscriptionId,
            'endDate' => $this->endDate?->format(\DateTimeInterface::ATOM),
            'interval' => $this->interval,
        ];
    }
}
