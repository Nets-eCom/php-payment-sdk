<?php

/**
 * Unscheduled subscription happy path.
 *
 * First run: creates a hosted payment with unscheduled subscription consent and prints the checkout URL.
 * Second run: after checkout is completed, retrieves the unscheduled subscription, creates a charge, and checks the charge status.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-post
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-unscheduledsubscriptions-unscheduledsubscriptionid-get
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-unscheduledsubscriptions-unscheduledsubscriptionid-charges-post
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-unscheduledsubscriptions-unscheduledsubscriptionid-charges-status-get
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\ChargeUnscheduledSubscription;
use NexiCheckout\Model\Request\Payment;
use NexiCheckout\Model\Request\Payment\HostedCheckout;
use NexiCheckout\Model\Request\Payment\MethodConfiguration;
use NexiCheckout\Model\Request\Payment\UnscheduledSubscription;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';
$idempotencyKey = exampleEnv('NEXI_IDEMPOTENCY_KEY');

// 1) Create payment and subscription API clients
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);
$subscriptionApi = $paymentApiFactory->createSubscriptionApi($secretKey, $liveMode);

if ($paymentId === 'your-payment-id') {
    // 2) Create hosted payment when no payment ID is available yet
    $hostedPayment = $paymentApi->createHostedPayment(createHostedPaymentRequestForUnscheduledSubscription());

    exampleWriteLine('Hosted payment created. Open the checkout URL, complete the payment, then rerun this file with NEXI_PAYMENT_ID set to the value below.', 'green');
    exampleWriteLine($hostedPayment->getHostedPaymentPageUrl());
    exampleWriteLine('Payment ID: ' . $hostedPayment->getPaymentId(), 'yellow');

    exit(0);
}

// 3) Retrieve the unscheduled subscription created by the completed payment
$paymentResult = $paymentApi->retrievePayment($paymentId);
$payment = $paymentResult->getPayment();
$unscheduledSubscription = $payment->getUnscheduledSubscription();

if (!$unscheduledSubscription instanceof \NexiCheckout\Model\Result\RetrievePayment\UnscheduledSubscription) {
    exampleWriteLine('The payment does not contain an unscheduled subscription yet. Complete the hosted checkout first, then rerun this example.', 'red');
    exit(1);
}

$unscheduledSubscriptionId = $unscheduledSubscription->getUnscheduledSubscriptionId();
$subscriptionResult = $subscriptionApi->retrieveUnscheduledSubscription($unscheduledSubscriptionId);
$paymentDetails = $subscriptionResult->getPaymentDetails();
$cardDetails = $paymentDetails->getCardDetails();

exampleWriteLine('Unscheduled subscription retrieved.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Unscheduled subscription ID: ' . $unscheduledSubscriptionId, 'yellow');
exampleWriteLine('Payment method: ' . $paymentDetails->getPaymentMethod(), 'yellow');
exampleWriteLine('Payment type: ' . $paymentDetails->getPaymentType()->value, 'yellow');

if ($cardDetails->getMaskedPan() !== null) {
    exampleWriteLine('Masked PAN: ' . $cardDetails->getMaskedPan());
}

// 4) Create a charge for the unscheduled subscription
$effectiveIdempotencyKey = $idempotencyKey ?? 'unscheduled-happy-path-' . $paymentId;
$chargeResult = $subscriptionApi->chargeUnscheduledSubscription(
    $unscheduledSubscriptionId,
    new ChargeUnscheduledSubscription(exampleCreateOrder('unscheduled-happy-path-charge'), null),
    $effectiveIdempotencyKey
);

exampleWriteLine('Unscheduled subscription charged.', 'green');
exampleWriteLine('Charge payment ID: ' . $chargeResult->getPaymentId(), 'yellow');
exampleWriteLine('Charge ID: ' . $chargeResult->getChargeId(), 'yellow');
exampleWriteLine('Idempotency key: ' . $effectiveIdempotencyKey, 'yellow');

// 5) Check the charge status using the same idempotency key
$statusResult = $subscriptionApi->retrieveUnscheduledSubscriptionChargeStatus(
    $unscheduledSubscriptionId,
    $effectiveIdempotencyKey
);

exampleWriteLine('Charge status retrieved.', 'green');
exampleWriteLine('Status payment ID: ' . $statusResult->getPaymentId(), 'yellow');
exampleWriteLine('Status charge ID: ' . $statusResult->getChargeId(), 'yellow');
exampleWriteLine('Completed: ' . ($statusResult->getCompleted() ? 'yes' : 'no'), 'yellow');

function createHostedPaymentRequestForUnscheduledSubscription(): Payment
{
    $exampleOrder = exampleCreateOrder('unscheduled-happy-path-order');
    // Optional env overrides; fallback URLs are demo placeholders.
    $returnUrl = exampleEnv('NEXI_RETURN_URL') ?? 'https://shop.example.com/returnUrl';
    $cancelUrl = exampleEnv('NEXI_CANCEL_URL') ?? 'https://shop.example.com/cancelUrl';
    $termsUrl = exampleEnv('NEXI_TERMS_URL') ?? 'https://shop.example.com/termsUrl';

    return new Payment(
        $exampleOrder,
        new HostedCheckout(
            $returnUrl,
            $cancelUrl,
            $termsUrl
        ),
        null,
        null,
        new UnscheduledSubscription(true),
        null,
        'sdk-example-' . bin2hex(random_bytes(4)),
        [
            new MethodConfiguration('card', true),
        ]
    );
}
