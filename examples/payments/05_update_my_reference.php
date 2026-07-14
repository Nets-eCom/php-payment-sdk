<?php

/**
 * Update myReference for a payment.
 *
 * Updates the merchant payment reference used to track payment-related actions in your own systems.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-myreference-put
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\MyReference;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';
$newMyReference = exampleEnv('NEXI_MY_REFERENCE') ?? 'accounting-reference-123';

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Update myReference
$paymentApi->updateMyReference($paymentId, new MyReference($newMyReference));

exampleWriteLine('myReference updated.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('myReference: ' . $newMyReference, 'yellow');
