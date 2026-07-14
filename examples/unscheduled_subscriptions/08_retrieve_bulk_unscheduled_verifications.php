<?php

/**
 * Retrieve bulk unscheduled subscription verifications.
 *
 * Retrieves verification results associated with a bulk unscheduled verification operation by bulk ID.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-unscheduledsubscriptions-verifications-bulkid-get
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$bulkId = exampleEnv('NEXI_BULK_ID') ?? 'your-bulk-id';
$skip = exampleEnv('NEXI_SKIP');
$skip = $skip === null ? null : (int) $skip;
$take = exampleEnv('NEXI_TAKE');
$take = $take === null ? null : (int) $take;
$pageNumber = exampleEnv('NEXI_PAGE_NUMBER');
$pageNumber = $pageNumber === null ? null : (int) $pageNumber;
$pageSize = exampleEnv('NEXI_PAGE_SIZE');
$pageSize = $pageSize === null ? null : (int) $pageSize;

// 1) Create subscription API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$subscriptionApi = $paymentApiFactory->createSubscriptionApi($secretKey, $liveMode);

// 2) Retrieve bulk unscheduled verifications
$bulkStatusResult = $subscriptionApi->retrieveBulkVerificationsForUnscheduledSubscriptions(
    $bulkId,
    $skip,
    $take,
    $pageNumber,
    $pageSize
);

exampleWriteLine('Bulk verification status retrieved.', 'green');
exampleWriteLine('Bulk verification ID: ' . $bulkId, 'yellow');
exampleWriteLine('Bulk operation status: ' . $bulkStatusResult->getBulkOperationStatus()->value, 'yellow');
exampleWriteLine('More results available: ' . ($bulkStatusResult->isMore() ? 'yes' : 'no'), 'yellow');

foreach ($bulkStatusResult->getPage() as $index => $verification) {
    exampleWriteLine(sprintf('Verification #%d', $index + 1), 'cyan');
    exampleWriteLine('Unscheduled subscription ID: ' . $verification->getUnscheduledSubscriptionId());
    exampleWriteLine('Status: ' . $verification->getVerificationStatus()->value);

    if ($verification->getExternalReference() !== null) {
        exampleWriteLine('External reference: ' . $verification->getExternalReference());
    }

    if ($verification->getPaymentId() !== null) {
        exampleWriteLine('Payment ID: ' . $verification->getPaymentId());
    }

    if ($verification->getMessage() !== null) {
        exampleWriteLine('Message: ' . $verification->getMessage());
    }

    if ($verification->getCode() !== null) {
        exampleWriteLine('Code: ' . $verification->getCode());
    }
}
