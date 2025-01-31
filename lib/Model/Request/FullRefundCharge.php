<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request;

class FullRefundCharge extends RefundCharge
{
    public function __construct(
        private readonly int $amount,
        ?string $myReference = null,
    ) {
        parent::__construct($myReference);
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
