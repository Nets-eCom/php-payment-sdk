<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\Shared\Notification;

final class Webhook implements \JsonSerializable
{
    /**
     * @param list<Header> $headers
     */
    public function __construct(
        private readonly string $eventName,
        private readonly string $url,
        private readonly string $authorization,
        private readonly array $headers = [],
    ) {
    }

    /**
     * @return array{
     *     eventName: string,
     *     url: string,
     *     authorization: string,
     *     headers: list<Header>
     * }
     */
    public function jsonSerialize(): array
    {
        $data = [
            'eventName' => $this->eventName,
            'url' => $this->url,
            'authorization' => $this->authorization,
        ];

        if ($this->headers !== []) {
            $data['headers'] = $this->headers;
        }

        return $data;
    }
}
