<?php

/**
 * Charge a single unscheduled subscription.
 *
 * Creates a new payment object and charges the specified amount for the unscheduled subscription.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-unscheduledsubscriptions-unscheduledsubscriptionid-charges-post
 */

declare(strict_types=1);

use NexiCheckout\Model\Result\RetrievePayment\UnscheduledSubscription;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\ChargeUnscheduledSubscription;

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

// 3) Charge unscheduled subscription
$chargeRequest = new ChargeUnscheduledSubscription(exampleCreateOrder('unscheduled-charge-order'), null);
$chargeResult = $subscriptionApi->chargeUnscheduledSubscription(
    $unscheduledSubscriptionId,
    $chargeRequest,
    $idempotencyKey
);

exampleWriteLine('Unscheduled subscription charged.', 'green');

if ($paymentId !== 'your-payment-id') {
    exampleWriteLine('Original payment ID: ' . $paymentId, 'yellow');
}

exampleWriteLine('Unscheduled subscription ID: ' . $unscheduledSubscriptionId, 'yellow');
exampleWriteLine('Charge payment ID: ' . $chargeResult->getPaymentId(), 'yellow');
exampleWriteLine('Charge ID: ' . $chargeResult->getChargeId(), 'yellow');

if ($idempotencyKey !== null) {
    exampleWriteLine('Idempotency key: ' . $idempotencyKey, 'yellow');
}

exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Check charge status: 04_check_charge_status.php');
exampleWriteLine('2) Retrieve unscheduled subscription details again: 02a_retrieve_unscheduled_subscription.php');
