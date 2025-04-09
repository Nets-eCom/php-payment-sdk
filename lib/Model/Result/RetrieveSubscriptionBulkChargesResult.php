<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Result\Shared\BulkOperationStatusEnum;
use NexiCheckout\Model\Result\SubscriptionCharges\ChargeStatusEnum;
use NexiCheckout\Model\Result\SubscriptionCharges\SubscriptionCharge;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class RetrieveSubscriptionBulkChargesResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    /**
     * @param list<SubscriptionCharge> $page
     */
    public function __construct(
        private readonly array $page,
        private readonly bool $more,
        private readonly BulkOperationStatusEnum $bulkOperationStatus,
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

    public function getBulkOperationStatus(): BulkOperationStatusEnum
    {
        return $this->bulkOperationStatus;
    }

    public static function fromJson(string $string): RetrieveSubscriptionBulkChargesResult
    {
        $payload = self::jsonDeserialize($string);

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
            $payload['page']
        );

        return new self($charges, $payload['more'], BulkOperationStatusEnum::from($payload['status']));
    }
}
