<?php

/**
 * Retrieve bulk subscription verifications.
 *
 * Retrieves verification results associated with a bulk verification operation by bulk ID.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-verifications-bulkid-get
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$bulkId = exampleEnv('NEXI_BULK_ID') ?? 'your-bulk-id';

// 1) Create subscription API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$subscriptionApi = $paymentApiFactory->createSubscriptionApi($secretKey, $liveMode);

// 2) Retrieve bulk verifications
$bulkStatusResult = $subscriptionApi->retrieveBulkVerifications($bulkId);

exampleWriteLine('Bulk verification status retrieved.', 'green');
exampleWriteLine('Bulk verification ID: ' . $bulkId, 'yellow');
exampleWriteLine('Bulk operation status: ' . $bulkStatusResult->getBulkOperationStatus()->value, 'yellow');
exampleWriteLine('More results available: ' . ($bulkStatusResult->isMore() ? 'yes' : 'no'), 'yellow');

foreach ($bulkStatusResult->getPage() as $index => $verification) {
    exampleWriteLine(sprintf('Verification #%d', $index + 1), 'cyan');
    exampleWriteLine('Subscription ID: ' . $verification->getSubscriptionId());
    exampleWriteLine('Status: ' . $verification->getVerificationStatusEnum()->value);

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
