<?php

/**
 * Scheduled subscription happy path.
 *
 * First run: creates a hosted payment with subscription consent and prints the checkout URL.
 * Second run: after checkout is completed, retrieves the subscription and creates the first recurring charge.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-post
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-subscriptionid-get
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-subscriptions-subscriptionid-charges-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\ChargeSubscription;
use NexiCheckout\Model\Request\Payment;
use NexiCheckout\Model\Request\Payment\HostedCheckout;
use NexiCheckout\Model\Request\Payment\MethodConfiguration;
use NexiCheckout\Model\Request\Payment\Subscription;

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
    $hostedPayment = $paymentApi->createHostedPayment(createHostedPaymentRequestForSubscription());

    exampleWriteLine('Hosted payment created. Open the checkout URL, complete the payment, then rerun this file with NEXI_PAYMENT_ID set to the value below.', 'green');
    exampleWriteLine($hostedPayment->getHostedPaymentPageUrl());
    exampleWriteLine('Payment ID: ' . $hostedPayment->getPaymentId(), 'yellow');

    exit(0);
}

// 3) Retrieve the subscription created by the completed payment
$paymentResult = $paymentApi->retrievePayment($paymentId);
$payment = $paymentResult->getPayment();
$subscription = $payment->getSubscription();

if (!$subscription instanceof \NexiCheckout\Model\Result\RetrievePayment\Subscription) {
    exampleWriteLine('The payment does not contain a scheduled subscription yet. Complete the hosted checkout first, then rerun this example.', 'red');
    exit(1);
}

$subscriptionId = $subscription->getId();
$subscriptionResult = $subscriptionApi->retrieveSubscription($subscriptionId);
$paymentDetails = $subscriptionResult->getPaymentDetails();
$cardDetails = $paymentDetails->getCardDetails();

exampleWriteLine('Subscription retrieved.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Subscription ID: ' . $subscriptionId, 'yellow');
exampleWriteLine('Interval: ' . $subscriptionResult->getInterval() . ' day(s)', 'yellow');
exampleWriteLine('End date: ' . $subscriptionResult->getEndDate()->format(DATE_ATOM), 'yellow');
exampleWriteLine('Payment method: ' . $paymentDetails->getPaymentMethod(), 'yellow');

if ($cardDetails->getMaskedPan() !== null) {
    exampleWriteLine('Masked PAN: ' . $cardDetails->getMaskedPan());
}

// 4) Create the first recurring charge.
// The first charge can only be created after the subscription's first interval has started.
$effectiveIdempotencyKey = $idempotencyKey ?? 'subscription-happy-path-' . $paymentId;
$chargeResult = $subscriptionApi->chargeSubscription(
    $subscriptionId,
    new ChargeSubscription(exampleCreateOrder('subscription-happy-path-charge'), null),
    $effectiveIdempotencyKey
);

exampleWriteLine('Subscription charged.', 'green');
exampleWriteLine('Charge payment ID: ' . $chargeResult->getPaymentId(), 'yellow');
exampleWriteLine('Charge ID: ' . $chargeResult->getChargeId(), 'yellow');
exampleWriteLine('Idempotency key: ' . $effectiveIdempotencyKey, 'yellow');

function createHostedPaymentRequestForSubscription(): Payment
{
    $exampleOrder = exampleCreateOrder('subscription-happy-path-order');
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
        new Subscription(
            null,
            new DateTimeImmutable('+1 year'),
            1
        ),
        null,
        null,
        'sdk-example-' . bin2hex(random_bytes(4)),
        [
            new MethodConfiguration('card', true),
        ]
    );
}
