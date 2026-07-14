<?php

/**
 * Refund a payment.
 *
 * Refunds a previously settled payment either in full or in part.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-refunds-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\RefundPayment;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';
$refundAmount = (int) (exampleEnv('NEXI_REFUND_AMOUNT') ?? '10000');
$idempotencyKey = exampleEnv('NEXI_IDEMPOTENCY_KEY');

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Refund payment
$refundResult = $paymentApi->refundPayment(
    $paymentId,
    new RefundPayment($refundAmount),
    $idempotencyKey
);

exampleWriteLine('Payment refunded.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Refund amount: ' . $refundAmount, 'yellow');
exampleWriteLine('Refund ID: ' . $refundResult->getRefundId(), 'yellow');

if ($idempotencyKey !== null) {
    exampleWriteLine('Idempotency key: ' . $idempotencyKey, 'yellow');
}

exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Retrieve payment and verify refund status: 02_retrieve_payment.php');
exampleWriteLine('2) If refund is pending, cancel it: 11_cancel_pending_refund.php');
