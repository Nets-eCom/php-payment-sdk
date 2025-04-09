<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Result\RetrieveBulkVerifications\Verification;
use NexiCheckout\Model\Result\RetrieveBulkVerifications\VerificationStatusEnum;
use NexiCheckout\Model\Result\Shared\BulkOperationStatusEnum;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class RetrieveBulkVerificationsResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    /**
     * @param list<Verification> $page
     */
    public function __construct(
        private readonly array $page,
        private readonly bool $more,
        private readonly BulkOperationStatusEnum $bulkOperationStatus
    ) {
    }

    /**
     * @return list<Verification>
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

    public static function fromJson(string $string): RetrieveBulkVerificationsResult
    {
        $data = self::jsonDeserialize($string);

        return new self(
            self::createPage($data['page']),
            $data['more'],
            BulkOperationStatusEnum::from($data['status'])
        );
    }

    /**
     * @param array{
     *     array{
     *         subscriptionId: string,
     *         status: string,
     *         externalReference?: string,
     *         message?: string,
     *         code?: string,
     *         paymentId?: string
     *     }
     * } $data
     *
     * @return list<Verification>
     */
    public static function createPage(array $data): array
    {
        $pages = [];

        foreach ($data as $verification) {
            $pages[] = new Verification(
                $verification['subscriptionId'],
                VerificationStatusEnum::from($verification['status']),
                $verification['externalReference'] ?? null,
                $verification['message'] ?? null,
                $verification['code'] ?? null,
                $verification['paymentId'] ?? null
            );
        }

        return $pages;
    }
}
