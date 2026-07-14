<?php

/**
 * Retrieve unscheduled subscription charge status.
 *
 * Retrieves the result of a single charge attempt for an unscheduled subscription.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-unscheduledsubscriptions-unscheduledsubscriptionid-charges-status-get
 */

declare(strict_types=1);

use NexiCheckout\Model\Result\RetrievePayment\UnscheduledSubscription;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';
$unscheduledSubscriptionId = exampleEnv('NEXI_UNSCHEDULED_SUBSCRIPTION_ID') ?? 'your-unscheduled-subscription-id';
$idempotencyKey = exampleEnv('NEXI_IDEMPOTENCY_KEY');

// 1) Create payment and subscription API clients
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);
$subscriptionApi = $paymentApiFactory->createSubscriptionApi($secretKey, $liveMode);

// 2) Retrieve the unscheduled subscription ID from the completed hosted payment when it is not provided directly
if ($unscheduledSubscriptionId === 'your-unscheduled-subscription-id') {
    $paymentResult = $paymentApi->retrievePayment($paymentId);
    $payment = $paymentResult->getPayment();
    $unscheduledSubscription = $payment->getUnscheduledSubscription();

    if (!$unscheduledSubscription instanceof UnscheduledSubscription) {
        exampleWriteLine('The retrieved payment does not contain an unscheduled subscription. Complete the hosted payment first and ensure it was created with UnscheduledSubscription enabled.', 'red');
        exit(1);
    }

    $unscheduledSubscriptionId = $unscheduledSubscription->getUnscheduledSubscriptionId();
}

// 3) Retrieve charge status
$statusResult = $subscriptionApi->retrieveUnscheduledSubscriptionChargeStatus(
    $unscheduledSubscriptionId,
    $idempotencyKey
);

exampleWriteLine('Charge status retrieved.', 'green');

if ($paymentId !== 'your-payment-id') {
    exampleWriteLine('Original payment ID: ' . $paymentId, 'yellow');
}

exampleWriteLine('Unscheduled subscription ID: ' . $unscheduledSubscriptionId, 'yellow');
exampleWriteLine('Status payment ID: ' . $statusResult->getPaymentId(), 'yellow');
exampleWriteLine('Status charge ID: ' . $statusResult->getChargeId(), 'yellow');

if ($idempotencyKey !== null) {
    exampleWriteLine('Idempotency key: ' . $idempotencyKey, 'yellow');
}

exampleWriteLine('Completed: ' . ($statusResult->getCompleted() ? 'yes' : 'no'));
