<?php

/**
 * Charge a payment.
 *
 * Charges the specified payment either fully or partially, depending on the request body.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-charges-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\FullCharge;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';
$chargeAmount = (int) (exampleEnv('NEXI_CHARGE_AMOUNT') ?? '10000');
$idempotencyKey = exampleEnv('NEXI_IDEMPOTENCY_KEY');
$myReference = exampleEnv('NEXI_MY_REFERENCE');

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Charge payment
$chargeResult = $paymentApi->charge(
    $paymentId,
    new FullCharge($chargeAmount, null, $myReference),
    $idempotencyKey
);

exampleWriteLine('Payment charged.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Charge amount: ' . $chargeAmount, 'yellow');
exampleWriteLine('Charge ID: ' . $chargeResult->getChargeId(), 'yellow');

if ($idempotencyKey !== null) {
    exampleWriteLine('Idempotency key: ' . $idempotencyKey, 'yellow');
}

exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Retrieve payment to inspect charges: 02_retrieve_payment.php');
exampleWriteLine('2) Refund from charge ID: 09_refund_charge.php');
exampleWriteLine('3) Refund by payment ID: 10_refund_payment.php');
