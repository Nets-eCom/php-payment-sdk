<?php

/**
 * Partially refund a payment by returning selected order items.
 *
 * The items and amount must match items from the original payment. The amount
 * is specified separately and should equal the items' grossTotalAmount total.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-refunds-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\Item;
use NexiCheckout\Model\Request\RefundPayment;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';
$idempotencyKey = exampleEnv('NEXI_IDEMPOTENCY_KEY');
$myReference = exampleEnv('NEXI_MY_REFERENCE');

// Replace these values with the exact item details from the original payment.
$refundItems = [
    new Item('Example item', 1, 'pcs', 5000, 5000, 5000, 'example-item-1'),
];
$refundAmount = array_sum(array_map(
    static fn (Item $item): int => $item->getGrossTotalAmount(),
    $refundItems
));

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Refund the selected order items
$refundResult = $paymentApi->refundPayment(
    $paymentId,
    new RefundPayment($refundAmount, $refundItems, $myReference),
    $idempotencyKey
);

exampleWriteLine('Payment partially refunded.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Refund amount: ' . $refundAmount, 'yellow');
exampleWriteLine('Refund ID: ' . $refundResult->getRefundId(), 'yellow');

if ($idempotencyKey !== null) {
    exampleWriteLine('Idempotency key: ' . $idempotencyKey, 'yellow');
}

exampleWriteLine('Retrieve payment and verify refund status: 02_retrieve_payment.php');
