<?php

/**
 * Retrieve bulk subscription charges.
 *
 * Retrieves charges associated with a bulk charge operation by bulk ID.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-charges-bulkid-get
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

// 2) Retrieve bulk charges
$bulkStatusResult = $subscriptionApi->retrieveSubscriptionBulkCharges($bulkId);

exampleWriteLine('Bulk charge status retrieved.', 'green');
exampleWriteLine('Bulk ID: ' . $bulkId, 'yellow');
exampleWriteLine('Bulk operation status: ' . $bulkStatusResult->getBulkOperationStatus()->value, 'yellow');
exampleWriteLine('More results available: ' . ($bulkStatusResult->isMore() ? 'yes' : 'no'), 'yellow');

foreach ($bulkStatusResult->getPage() as $index => $charge) {
    exampleWriteLine(sprintf('Charge #%d', $index + 1), 'cyan');
    exampleWriteLine('Subscription ID: ' . $charge->getSubscriptionId());
    exampleWriteLine('Status: ' . $charge->getChargeStatus()->value);

    if ($charge->getPaymentId() !== null) {
        exampleWriteLine('Payment ID: ' . $charge->getPaymentId());
    }

    if ($charge->getChargeId() !== null) {
        exampleWriteLine('Charge ID: ' . $charge->getChargeId());
    }

    if ($charge->getExternalReference() !== null) {
        exampleWriteLine('External reference: ' . $charge->getExternalReference());
    }

    if ($charge->getMessage() !== null) {
        exampleWriteLine('Message: ' . $charge->getMessage());
    }

    if ($charge->getCode() !== null) {
        exampleWriteLine('Code: ' . $charge->getCode());
    }

    if ($charge->getSource() !== null) {
        exampleWriteLine('Source: ' . $charge->getSource());
    }
}
