<?php

/**
 * Create an embedded payment.
 *
 * Initializes a new payment object for the checkout flow using the embedded checkout integration.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\Payment;
use NexiCheckout\Model\Request\Payment\EmbeddedCheckout;
use NexiCheckout\Model\Request\Payment\MethodConfiguration;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Create embedded payment
$embeddedPayment = $paymentApi->createEmbeddedPayment(
    createEmbeddedPaymentRequest()
);

exampleWriteLine('Embedded payment created.', 'green');
exampleWriteLine('Payment ID: ' . $embeddedPayment->getPaymentId(), 'yellow');
exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Retrieve details: 02_retrieve_payment.php');
exampleWriteLine('2) Update data before completion: 03_update_reference_information.php, 04_update_order.php, or 05_update_my_reference.php');
exampleWriteLine('3) Finalize flow after checkout: 08_charge_payment.php, 09_refund_charge.php, or 10_refund_payment.php');

function createEmbeddedPaymentRequest(): Payment
{
    $exampleOrder = exampleCreateOrder();

    // Optional env overrides; fallback URLs are demo placeholders.
    $checkoutUrl = exampleEnv('NEXI_CHECKOUT_URL') ?? 'https://shop.example.com/checkout';
    $termsUrl = exampleEnv('NEXI_TERMS_URL') ?? 'https://shop.example.com/termsUrl';

    return new Payment(
        $exampleOrder,
        new EmbeddedCheckout(
            $checkoutUrl,
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
