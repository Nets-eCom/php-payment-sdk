<?php

/**
 * Bulk charge subscriptions.
 *
 * Charges multiple subscriptions in one request and returns a bulk ID for status tracking.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-charges-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\BulkChargeSubscription;
use NexiCheckout\Model\Request\BulkChargeSubscription\Subscription as BulkSubscription;
use NexiCheckout\Model\Request\Shared\Notification;
use NexiCheckout\Model\Request\Shared\Notification\Webhook;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentIds = exampleEnvList('NEXI_PAYMENT_IDS') ?? [
    'your-payment-id-1',
    'your-payment-id-2',
];
$externalBulkChargeId = exampleEnv('NEXI_EXTERNAL_BULK_CHARGE_ID');

if ($externalBulkChargeId === null) {
    $externalBulkChargeId = 'sdk-subscription-bulk-' . bin2hex(random_bytes(8));
}

// 1) Create payment and subscription API clients
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);
$subscriptionApi = $paymentApiFactory->createSubscriptionApi($secretKey, $liveMode);

// 2) Retrieve the subscription IDs from the completed hosted payments
$subscriptionIds = [];
foreach ($paymentIds as $paymentId) {
    $paymentResult = $paymentApi->retrievePayment($paymentId);
    $payment = $paymentResult->getPayment();
    $subscription = $payment->getSubscription();

    if (!$subscription instanceof \NexiCheckout\Model\Result\RetrievePayment\Subscription) {
        exampleWriteLine('The retrieved payment with ID ' . $paymentId . ' does not contain a scheduled subscription. Skipping this payment.', 'red');
        continue;
    }

    $subscriptionIds[] = $subscription->getId();
}

if ($subscriptionIds === []) {
    exampleWriteLine('No scheduled subscriptions were found for the configured payment IDs.', 'red');
    exit(1);
}

// 3) Create bulk charge request for the subscription IDs retrieved above
$subscriptions = [];
foreach ($subscriptionIds as $index => $subscriptionId) {
    $subscriptions[] = new BulkSubscription(
        $subscriptionId,
        sprintf('sdk-bulk-charge-%d', $index + 1),
        exampleCreateOrder(sprintf('subscription-bulk-order-%d', $index + 1))
    );
}

// Optional env overrides; fallback webhook values are demo placeholders.
$webhookUrl = exampleEnv('NEXI_WEBHOOK_URL') ?? 'https://example.com/webhooks/payment-charge-created';
$webhookAuthorization = exampleEnv('NEXI_WEBHOOK_AUTHORIZATION') ?? 'Bearer replace-me';

$notification = new Notification([
    new Webhook('payment.charge.created', $webhookUrl, $webhookAuthorization),
]);

$bulkChargeRequest = new BulkChargeSubscription(
    $externalBulkChargeId,
    $notification,
    $subscriptions
);

// 4) Bulk charge the subscriptions configured above
$bulkChargeResult = $subscriptionApi->bulkChargeSubscription($bulkChargeRequest);

exampleWriteLine('Bulk charge accepted.', 'green');
exampleWriteLine('Bulk ID: ' . $bulkChargeResult->getBulkId(), 'yellow');
exampleWriteLine('External bulk charge ID: ' . $externalBulkChargeId, 'yellow');
exampleWriteLine('Charged subscriptions: ' . implode(', ', $subscriptionIds), 'yellow');
exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Check bulk charge status: 05_retrieve_bulk_charges.php');
exampleWriteLine('2) Run a verification batch for the same IDs: 06_verify_subscriptions.php');
