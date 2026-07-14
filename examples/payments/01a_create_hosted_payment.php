<?php

/**
 * Create a hosted payment.
 *
 * Initializes a new payment object for the checkout flow and returns a hosted payment page URL.
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

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Create hosted payment
$hostedPayment = $paymentApi->createHostedPayment(
    createHostedPaymentRequest()
);
$paymentId = $hostedPayment->getPaymentId();

exampleWriteLine('Hosted payment created. Complete it in a browser by visiting the URL below:', 'green');
exampleWriteLine($hostedPayment->getHostedPaymentPageUrl());
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Retrieve details: 02_retrieve_payment.php');
exampleWriteLine('2) Update data before completion: 03_update_reference_information.php, 04_update_order.php, or 05_update_my_reference.php');
exampleWriteLine('3) Finalize flow after checkout: 08_charge_payment.php, 09_refund_charge.php, or 10_refund_payment.php');

function createHostedPaymentRequest(): Payment
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
        null,
        null,
        'sdk-example-' . bin2hex(random_bytes(4)),
        [
            new MethodConfiguration('card', true),
        ]
    );
}
