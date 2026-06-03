<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Result\RetrieveBulkUnscheduledCharges\UnscheduledCharge;
use NexiCheckout\Model\Result\Shared\BulkOperationStatusEnum;
use NexiCheckout\Model\Result\SubscriptionCharges\ChargeStatusEnum;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class RetrieveBulkUnscheduledChargesResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    /**
     * @param list<UnscheduledCharge> $page
     */
    public function __construct(
        private readonly array $page,
        private readonly bool $more,
        private readonly BulkOperationStatusEnum $bulkOperationStatus,
    ) {
    }

    /**
     * @return list<UnscheduledCharge>
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

    public static function fromJson(string $string): RetrieveBulkUnscheduledChargesResult
    {
        $data = self::jsonDeserialize($string);

        $page = array_map(
            fn (array $item) => new UnscheduledCharge(
                $item['unscheduledSubscriptionId'],
                ChargeStatusEnum::from($item['status']),
                $item['paymentId'] ?? null,
                $item['chargeId'] ?? null,
                $item['message'] ?? null,
                $item['code'] ?? null,
                $item['source'] ?? null,
                $item['externalReference'] ?? null,
            ),
            $data['page']
        );

        return new self($page, $data['more'], BulkOperationStatusEnum::from($data['status']));
    }
}
