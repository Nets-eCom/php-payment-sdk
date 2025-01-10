<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrievePayment;

enum RefundStateEnum: string
{
    case PENDING = 'Pending';
    case CANCELLED = 'Cancelled';
    case FAILED = 'Failed';

    case COMPLETED = 'Completed';
    case EXPIRED = 'Expired';
}
