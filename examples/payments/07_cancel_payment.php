<?php

/**
 * Cancel a payment.
 *
 * Supports both full and partial cancellations. If the amount does not match the full order amount,
 * the API treats the request as a partial cancellation.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-cancels-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\Cancel;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';
$cancelAmount = (int) (exampleEnv('NEXI_CANCEL_AMOUNT') ?? '10000');

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Cancel payment
$paymentApi->cancel($paymentId, new Cancel($cancelAmount));

exampleWriteLine('Payment cancelled.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Cancelled amount: ' . $cancelAmount, 'yellow');
