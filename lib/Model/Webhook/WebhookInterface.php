<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook;

use NexiCheckout\Model\Webhook\Shared\Data;

interface WebhookInterface
{
    public function getEvent(): EventNameEnum;

    public function getData(): Data;
}
