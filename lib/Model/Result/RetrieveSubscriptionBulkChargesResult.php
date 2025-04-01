<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Result\SubscriptionCharges\ChargeStatusEnum;
use NexiCheckout\Model\Result\SubscriptionCharges\SubscriptionCharge;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class RetrieveSubscriptionBulkChargesResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(
        private readonly array $page,
        private readonly bool $more,
        private readonly string $status,
    ) {
    }

    /**
     * @return list<SubscriptionCharge>
     */
    public function getPage(): array
    {
        return $this->page;
    }

    public function isMore(): bool
    {
        return $this->more;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public static function fromJson(string $json): RetrieveSubscriptionBulkChargesResult
    {
        $payload = self::jsonDeserialize($json);

        $charges = array_map(
            fn (array $charge) => new SubscriptionCharge(
                $charge['subscriptionId'],
                ChargeStatusEnum::from($charge['status']),
                $charge['paymentId'] ?? null,
                $charge['chargeId'] ?? null,
                $charge['message'] ?? null,
                $charge['code'] ?? null,
                $charge['source'] ?? null,
                $charge['externalReference'] ?? null
            ),
            $payload['charges']
        );

        return new self($charges, $payload['more'], $payload['status']);
    }
}
