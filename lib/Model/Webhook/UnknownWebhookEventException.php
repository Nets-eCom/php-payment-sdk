<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

final class UnknownWebhookEventException extends \RuntimeException
{
    public function __construct(string $eventName)
    {
        parent::__construct(sprintf('Unknown webhook event: "%s"', $eventName));
    }
}
