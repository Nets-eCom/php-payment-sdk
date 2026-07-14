<?php

/**
 * Retrieve a subscription by external reference.
 *
 * This method is intended for subscriptions imported from other payment platforms.
 * Subscriptions created in Checkout do not have an external reference value.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-get
 */

declare(strict_types=1);

use NexiCheckout\Model\Result\RetrievePayment\Subscription;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';
$externalReference = exampleEnv('NEXI_EXTERNAL_REFERENCE') ?? 'your-external-reference';

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

// 3) Retrieve subscription by external reference
$subscriptionResult = $subscriptionApi->retrieveSubscriptionByExternalReference(
    $subscriptionId,
    $externalReference
);

exampleWriteLine('Subscription retrieved by external reference.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Subscription ID: ' . $subscriptionResult->getSubscriptionId(), 'yellow');
exampleWriteLine('External reference: ' . $externalReference, 'yellow');
exampleWriteLine('Interval: ' . $subscriptionResult->getInterval() . ' day(s)', 'yellow');
exampleWriteLine('End date: ' . $subscriptionResult->getEndDate()->format(DATE_ATOM), 'yellow');
