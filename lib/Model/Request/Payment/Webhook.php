<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Payment;

final class Webhook implements \JsonSerializable
{
    public function __construct(
        private readonly string $eventName,
        private readonly string $url,
        private readonly string $authorization,
    ) {
    }

    /**
     * @return array{
     *     eventName: string,
     *     url: string,
     *     authorization: string
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'eventName' => $this->eventName,
            'url' => $this->url,
            'authorization' => $this->authorization,
        ];
    }
}
