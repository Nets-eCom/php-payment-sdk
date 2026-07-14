<?php

/**
 * Retrieve a subscription by subscription ID.
 *
 * Gets an existing subscription using the subscription ID derived from a completed payment.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-subscriptionid-get
 */

declare(strict_types=1);

use NexiCheckout\Model\Result\RetrievePayment\Subscription;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';

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

// 3) Retrieve subscription
$subscriptionResult = $subscriptionApi->retrieveSubscription($subscriptionId);
$paymentDetails = $subscriptionResult->getPaymentDetails();
$cardDetails = $paymentDetails->getCardDetails();

exampleWriteLine('Subscription retrieved.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Subscription ID: ' . $subscriptionResult->getSubscriptionId(), 'yellow');
exampleWriteLine('Interval: ' . $subscriptionResult->getInterval() . ' day(s)', 'yellow');
exampleWriteLine('End date: ' . $subscriptionResult->getEndDate()->format(DATE_ATOM), 'yellow');
exampleWriteLine('Payment method: ' . $paymentDetails->getPaymentMethod(), 'yellow');

if ($cardDetails->getMaskedPan() !== null) {
    exampleWriteLine('Masked PAN: ' . $cardDetails->getMaskedPan());
}

if ($cardDetails->getExpiryDate() !== null) {
    exampleWriteLine('Expiry date: ' . $cardDetails->getExpiryDate());
}

if ($subscriptionResult->getFrequency() !== null) {
    exampleWriteLine('Frequency: ' . $subscriptionResult->getFrequency());
}

exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Charge this subscription: 03_charge_subscription.php');
exampleWriteLine('2) Bulk charge multiple subscriptions: 04_bulk_charge_subscriptions.php');
exampleWriteLine('3) Verify multiple subscriptions: 06_verify_subscriptions.php');
