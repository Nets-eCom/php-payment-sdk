<?php

/**
 * Verify subscriptions in bulk.
 *
 * Verifies a set of subscriptions and returns a bulk ID that can be used to check verification status.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-verifications-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\VerifySubscriptions;
use NexiCheckout\Model\Request\VerifySubscriptions\Subscription as VerifySubscription;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentIds = exampleEnvList('NEXI_PAYMENT_IDS') ?? [
    'your-payment-id-1',
    'your-payment-id-2',
];
$externalBulkVerificationId = exampleEnv('NEXI_EXTERNAL_BULK_VERIFICATION_ID');

if ($externalBulkVerificationId === null) {
    $externalBulkVerificationId = 'sdk-subscription-verify-' . bin2hex(random_bytes(8));
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

// 3) Create verification request for the subscription IDs retrieved above
$subscriptions = [];
foreach ($subscriptionIds as $subscriptionId) {
    $subscriptions[] = new VerifySubscription($subscriptionId);
}

$verificationRequest = new VerifySubscriptions($subscriptions, $externalBulkVerificationId);

// 4) Verify the subscriptions configured above
$verificationResult = $subscriptionApi->verifySubscriptions($verificationRequest);

exampleWriteLine('Subscription verification accepted.', 'green');
exampleWriteLine('Bulk verification ID: ' . $verificationResult->getBulkId(), 'yellow');
exampleWriteLine('External bulk verification ID: ' . $externalBulkVerificationId, 'yellow');
exampleWriteLine('Verified subscriptions: ' . implode(', ', $subscriptionIds), 'yellow');
exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Check bulk verification status: 07_retrieve_bulk_verifications.php');
exampleWriteLine('2) Run a bulk charge batch for the same IDs: 04_bulk_charge_subscriptions.php');
