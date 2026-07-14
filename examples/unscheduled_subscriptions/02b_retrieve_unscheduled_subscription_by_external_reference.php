<?php

/**
 * Retrieve an unscheduled subscription by external reference.
 *
 * This method is intended for unscheduled subscriptions imported from other payment platforms.
 * Unscheduled subscriptions created in Checkout do not have an external reference value.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-unscheduledsubscriptions-get
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$externalReference = exampleEnv('NEXI_EXTERNAL_REFERENCE') ?? 'your-external-reference';

// 1) Create subscription API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$subscriptionApi = $paymentApiFactory->createSubscriptionApi($secretKey, $liveMode);

// 2) Retrieve unscheduled subscription by external reference
$subscriptionResult = $subscriptionApi->retrieveUnscheduledSubscriptionByExternalReference($externalReference);
$paymentDetails = $subscriptionResult->getPaymentDetails();
$cardDetails = $paymentDetails->getCardDetails();

exampleWriteLine('Unscheduled subscription retrieved by external reference.', 'green');
exampleWriteLine('External reference: ' . $externalReference, 'yellow');
exampleWriteLine('Unscheduled subscription ID: ' . $subscriptionResult->getUnscheduledSubscriptionId(), 'yellow');
exampleWriteLine('Payment method: ' . $paymentDetails->getPaymentMethod(), 'yellow');
exampleWriteLine('Payment type: ' . $paymentDetails->getPaymentType()->value, 'yellow');

if ($cardDetails->getMaskedPan() !== null) {
    exampleWriteLine('Masked PAN: ' . $cardDetails->getMaskedPan());
}

if ($cardDetails->getExpiryDate() !== null) {
    exampleWriteLine('Expiry date: ' . $cardDetails->getExpiryDate());
}
