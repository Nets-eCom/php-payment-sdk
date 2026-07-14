<?php

/**
 * Retrieve an unscheduled subscription by ID.
 *
 * Gets an existing unscheduled subscription using the ID derived from a completed payment.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-unscheduledsubscriptions-unscheduledsubscriptionid-get
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

// 3) Retrieve unscheduled subscription
$subscriptionResult = $subscriptionApi->retrieveUnscheduledSubscription($unscheduledSubscriptionId);
$paymentDetails = $subscriptionResult->getPaymentDetails();
$cardDetails = $paymentDetails->getCardDetails();

exampleWriteLine('Unscheduled subscription retrieved.', 'green');

if ($paymentId !== 'your-payment-id') {
    exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
}

exampleWriteLine('Unscheduled subscription ID: ' . $subscriptionResult->getUnscheduledSubscriptionId(), 'yellow');
exampleWriteLine('Payment method: ' . $paymentDetails->getPaymentMethod(), 'yellow');
exampleWriteLine('Payment type: ' . $paymentDetails->getPaymentType()->value, 'yellow');

if ($cardDetails->getMaskedPan() !== null) {
    exampleWriteLine('Masked PAN: ' . $cardDetails->getMaskedPan());
}

if ($cardDetails->getExpiryDate() !== null) {
    exampleWriteLine('Expiry date: ' . $cardDetails->getExpiryDate());
}

exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Charge this unscheduled subscription: 03_charge_unscheduled_subscription.php');
exampleWriteLine('2) Retrieve charge status after charging: 04_check_charge_status.php');
exampleWriteLine('3) If you have many IDs, run bulk charge/verify: 05_bulk_charge_unscheduled_subscriptions.php or 07_verify_unscheduled_subscriptions.php');
