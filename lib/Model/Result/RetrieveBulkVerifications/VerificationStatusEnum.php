<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrieveBulkVerifications;

enum VerificationStatusEnum: string
{
    case PENDING = 'Pending';
    case SUCCEEDED = 'Succeeded';
    case FAILED = 'Failed';
}
