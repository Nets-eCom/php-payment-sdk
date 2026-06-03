<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Result\RetrieveBulkUnscheduledVerifications\UnscheduledVerification;
use NexiCheckout\Model\Result\Shared\BulkOperationStatusEnum;
use NexiCheckout\Model\Result\Shared\VerificationStatusEnum;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class RetrieveBulkUnscheduledVerificationsResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    /**
     * @param list<UnscheduledVerification> $page
     */
    public function __construct(
        private readonly array $page,
        private readonly bool $more,
        private readonly BulkOperationStatusEnum $bulkOperationStatus,
    ) {
    }

    /**
     * @return list<UnscheduledVerification>
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

    public static function fromJson(string $string): RetrieveBulkUnscheduledVerificationsResult
    {
        $data = self::jsonDeserialize($string);

        $page = array_map(
            fn (array $item) => new UnscheduledVerification(
                $item['unscheduledSubscriptionId'],
                VerificationStatusEnum::from($item['status']),
                $item['externalReference'] ?? null,
                $item['message'] ?? null,
                $item['code'] ?? null,
                $item['paymentId'] ?? null,
            ),
            $data['page']
        );

        return new self($page, $data['more'], BulkOperationStatusEnum::from($data['status']));
    }
}
