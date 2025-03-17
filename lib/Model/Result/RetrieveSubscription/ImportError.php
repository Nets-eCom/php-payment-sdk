<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\RetrieveSubscription;

class ImportError
{
    public function __construct(
        private readonly string $importStepsResponseCode,
        private readonly string $importStepsResponseSource,
        private readonly string $importStepsResponseText
    ) {
    }

    public function getImportStepsResponseCode(): string
    {
        return $this->importStepsResponseCode;
    }

    public function getImportStepsResponseSource(): string
    {
        return $this->importStepsResponseSource;
    }

    public function getImportStepsResponseText(): string
    {
        return $this->importStepsResponseText;
    }
}
