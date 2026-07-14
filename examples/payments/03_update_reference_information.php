<?php

/**
 * Update reference information for a payment.
 *
 * Updates the payment with a new order reference and checkout URL.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-referenceinformation-put
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\ReferenceInformation;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';
$checkoutUrl = exampleEnv('NEXI_CHECKOUT_URL') ?? 'https://test.checkout.dibspayment.eu/';
$newOrderReference = exampleEnv('NEXI_ORDER_REFERENCE') ?? 'updated-order-reference';

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Update reference information
$paymentApi->updateReferenceInformation(
    $paymentId,
    new ReferenceInformation($checkoutUrl, $newOrderReference)
);

exampleWriteLine('Reference information updated.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Checkout URL: ' . $checkoutUrl, 'yellow');
exampleWriteLine('Reference: ' . $newOrderReference, 'yellow');
