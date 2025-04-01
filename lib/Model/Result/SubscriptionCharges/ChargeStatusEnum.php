<?php declare(strict_types=1);

namespace NexiCheckout\Model\Result\SubscriptionCharges;

enum ChargeStatusEnum: string
{
    case PENDING = 'Pending';
    case SUCCEEDED = 'Succeeded';
    case FAILED = 'Failed';
}
