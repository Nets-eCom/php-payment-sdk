<?php

/**
 * Create a hosted payment that includes an unscheduled subscription agreement.
 *
 * Initializes a payment object with unscheduled subscription details so the resulting payment can be used for later variable-amount charges.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\Payment;
use NexiCheckout\Model\Request\Payment\HostedCheckout;
use NexiCheckout\Model\Request\Payment\MethodConfiguration;
use NexiCheckout\Model\Request\Payment\UnscheduledSubscription;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Create hosted payment with unscheduled subscription
$hostedPayment = $paymentApi->createHostedPayment(
    createHostedPaymentRequestForUnscheduledSubscription()
);
$paymentId = $hostedPayment->getPaymentId();

exampleWriteLine('Hosted payment created. Complete it in a browser by visiting the URL below:', 'green');
exampleWriteLine($hostedPayment->getHostedPaymentPageUrl());
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Retrieve details: 02a_retrieve_unscheduled_subscription.php');
exampleWriteLine('2) Charge it now: 03_charge_unscheduled_subscription.php');
exampleWriteLine('3) Collect multiple payment IDs and run bulk charge/verify: 05_bulk_charge_unscheduled_subscriptions.php or 07_verify_unscheduled_subscriptions.php');

function createHostedPaymentRequestForUnscheduledSubscription(): Payment
{
    $exampleOrder = exampleCreateOrder();

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
        'sdk-example-' . bin2hex(random_bytes(4)), // my reference
        [
            new MethodConfiguration('card', true),
        ]
    );
}
