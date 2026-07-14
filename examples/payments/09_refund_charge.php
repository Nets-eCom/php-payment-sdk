<?php

/**
 * Refund a charge.
 *
 * Refunds a previously settled transaction in full or in part using the charge ID returned from a charge.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-charges-chargeid-refunds-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\FullRefundCharge;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$chargeId = exampleEnv('NEXI_CHARGE_ID') ?? 'your-charge-id';
$refundAmount = (int) (exampleEnv('NEXI_REFUND_AMOUNT') ?? '10000');
$idempotencyKey = exampleEnv('NEXI_IDEMPOTENCY_KEY');

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Refund charge
$refundResult = $paymentApi->refundCharge(
    $chargeId,
    new FullRefundCharge($refundAmount),
    $idempotencyKey
);

exampleWriteLine('Charge refunded.', 'green');
exampleWriteLine('Charge ID: ' . $chargeId, 'yellow');
exampleWriteLine('Refund amount: ' . $refundAmount, 'yellow');
exampleWriteLine('Refund ID: ' . $refundResult->getRefundId(), 'yellow');

if ($idempotencyKey !== null) {
    exampleWriteLine('Idempotency key: ' . $idempotencyKey, 'yellow');
}

exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) Retrieve payment and verify refund status: 02_retrieve_payment.php');
exampleWriteLine('2) If refund is pending, cancel it: 11_cancel_pending_refund.php');
