<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

use NexiCheckout\Model\Request\VerifyUnscheduledSubscriptions\UnscheduledSubscription;

class VerifyUnscheduledSubscriptions implements \JsonSerializable
{
    /**
     * @param list<UnscheduledSubscription> $unscheduledSubscriptions
     */
    public function __construct(
        private readonly array $unscheduledSubscriptions,
        private readonly ?string $externalBulkVerificationId = null
    ) {
    }

    /**
     * @return array{
     *     externalBulkVerificationId: ?string,
     *     unscheduledSubscriptions: UnscheduledSubscription[]
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'externalBulkVerificationId' => $this->externalBulkVerificationId,
            'unscheduledSubscriptions' => $this->unscheduledSubscriptions,
        ];
    }
}
