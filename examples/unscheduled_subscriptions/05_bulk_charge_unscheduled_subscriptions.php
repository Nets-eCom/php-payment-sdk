<?php

/**
 * Bulk charge unscheduled subscriptions.
 *
 * Charges multiple unscheduled subscriptions in one request and returns a bulk ID for status tracking.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-unscheduledsubscriptions-charges-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\BulkChargeUnscheduledSubscription;
use NexiCheckout\Model\Request\BulkChargeUnscheduledSubscription\UnscheduledSubscription as BulkUnscheduledSubscription;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentIds = exampleEnvList('NEXI_PAYMENT_IDS') ?? [
    'your-payment-id-1',
    'your-payment-id-2',
];
$externalBulkChargeId = exampleEnv('NEXI_EXTERNAL_BULK_CHARGE_ID');

if ($externalBulkChargeId === null) {
    $externalBulkChargeId = 'sdk-unscheduled-bulk-' . bin2hex(random_bytes(8));
}

// 1) Create payment and subscription API clients
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);
$subscriptionApi = $paymentApiFactory->createSubscriptionApi($secretKey, $liveMode);

// 2) Retrieve the unscheduled subscription IDs from the completed hosted payments
$unscheduledSubscriptionIds = [];
foreach ($paymentIds as $paymentId) {
    $paymentResult = $paymentApi->retrievePayment($paymentId);
    $payment = $paymentResult->getPayment();
    $unscheduledSubscription = $payment->getUnscheduledSubscription();

    if (!$unscheduledSubscription instanceof \NexiCheckout\Model\Result\RetrievePayment\UnscheduledSubscription) {
        exampleWriteLine('The retrieved payment with ID ' . $paymentId . ' does not contain an unscheduled subscription. Skipping this payment.', 'red');
        continue;
    }

    $unscheduledSubscriptionIds[] = $unscheduledSubscription->getUnscheduledSubscriptionId();
}

if ($unscheduledSubscriptionIds === []) {
    exampleWriteLine('No unscheduled subscriptions were found for the configured payment IDs.', 'red');
    exit(1);
}

// 3) Create bulk charge request for the unscheduled subscription IDs retrieved above
$subscriptions = [];
foreach ($unscheduledSubscriptionIds as $index => $unscheduledSubscriptionId) {
    $subscriptions[] = new BulkUnscheduledSubscription(
        exampleCreateOrder(sprintf('unscheduled-bulk-order-%d', $index + 1)),
        $unscheduledSubscriptionId,
        null,
        sprintf('sdk-unscheduled-bulk-charge-%d', $index + 1)
    );
}

$bulkChargeRequest = new BulkChargeUnscheduledSubscription($subscriptions, $externalBulkChargeId);

// 4) Bulk charge the unscheduled subscriptions configured above
$bulkChargeResult = $subscriptionApi->bulkChargeUnscheduledSubscriptions($bulkChargeRequest);

exampleWriteLine('Bulk charge accepted.', 'green');
exampleWriteLine('Bulk ID: ' . $bulkChargeResult->getBulkId(), 'yellow');
exampleWriteLine('External bulk charge ID: ' . $externalBulkChargeId, 'yellow');
exampleWriteLine('Charged unscheduled subscriptions: ' . implode(', ', $unscheduledSubscriptionIds), 'yellow');
exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Check bulk charge status: 06_retrieve_bulk_unscheduled_charges.php');
exampleWriteLine('2) Run a verification batch for the same IDs: 07_verify_unscheduled_subscriptions.php');
