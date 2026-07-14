<?php

/**
 * Charge a single subscription.
 *
 * Creates a new payment object and charges the specified amount for the subscription.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-subscriptionid-charges-post
 */

declare(strict_types=1);

use NexiCheckout\Model\Result\RetrievePayment\Subscription;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\ChargeSubscription;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';
$idempotencyKey = exampleEnv('NEXI_IDEMPOTENCY_KEY');

// 1) Create payment and subscription API clients
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);
$subscriptionApi = $paymentApiFactory->createSubscriptionApi($secretKey, $liveMode);

// 2) Retrieve the subscription ID from the completed hosted payment
$paymentResult = $paymentApi->retrievePayment($paymentId);
$payment = $paymentResult->getPayment();
$subscription = $payment->getSubscription();

if (!$subscription instanceof Subscription) {
    exampleWriteLine('The retrieved payment does not contain a scheduled subscription. Complete the hosted payment first and ensure it was created with Subscription enabled.', 'red');
    exit(1);
}

$subscriptionId = $subscription->getId();

// 3) Charge subscription
$chargeRequest = new ChargeSubscription(exampleCreateOrder('subscription-charge-order'), null);
$chargeResult = $subscriptionApi->chargeSubscription(
    $subscriptionId,
    $chargeRequest,
    $idempotencyKey
);

exampleWriteLine('Subscription charged.', 'green');
exampleWriteLine('Original payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Subscription ID: ' . $subscriptionId, 'yellow');
exampleWriteLine('Charge payment ID: ' . $chargeResult->getPaymentId(), 'yellow');
exampleWriteLine('Charge ID: ' . $chargeResult->getChargeId(), 'yellow');

if ($idempotencyKey !== null) {
    exampleWriteLine('Idempotency key: ' . $idempotencyKey, 'yellow');
}
