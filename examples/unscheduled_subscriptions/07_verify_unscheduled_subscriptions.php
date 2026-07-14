<?php

/**
 * Verify unscheduled subscriptions in bulk.
 *
 * Verifies a set of unscheduled subscriptions and returns a bulk ID that can be used to check verification status.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-unscheduledsubscriptions-verifications-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\VerifyUnscheduledSubscriptions;
use NexiCheckout\Model\Request\VerifyUnscheduledSubscriptions\UnscheduledSubscription as VerifyUnscheduledSubscription;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentIds = exampleEnvList('NEXI_PAYMENT_IDS') ?? [
    'your-payment-id-1',
    'your-payment-id-2',
];
$externalBulkVerificationId = exampleEnv('NEXI_EXTERNAL_BULK_VERIFICATION_ID');

if ($externalBulkVerificationId === null) {
    $externalBulkVerificationId = 'sdk-unscheduled-verify-' . bin2hex(random_bytes(8));
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

// 3) Create verification request for the unscheduled subscription IDs retrieved above
$subscriptions = [];
foreach ($unscheduledSubscriptionIds as $unscheduledSubscriptionId) {
    $subscriptions[] = new VerifyUnscheduledSubscription($unscheduledSubscriptionId);
}

$verificationRequest = new VerifyUnscheduledSubscriptions($subscriptions, $externalBulkVerificationId);

// 4) Verify the unscheduled subscriptions configured above
$verificationResult = $subscriptionApi->verifyUnscheduledSubscriptions($verificationRequest);

exampleWriteLine('Unscheduled subscription verification accepted.', 'green');
exampleWriteLine('Bulk verification ID: ' . $verificationResult->getBulkId(), 'yellow');
exampleWriteLine('External bulk verification ID: ' . $externalBulkVerificationId, 'yellow');
exampleWriteLine('Verified unscheduled subscriptions: ' . implode(', ', $unscheduledSubscriptionIds), 'yellow');
exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Check bulk verification status: 08_retrieve_bulk_unscheduled_verifications.php');
exampleWriteLine('2) Run a bulk charge batch for the same IDs: 05_bulk_charge_unscheduled_subscriptions.php');
