<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\Shared;

enum BulkOperationStatusEnum: string
{
    case DONE = 'Done';
    case PROCESSING = 'Processing';
}
