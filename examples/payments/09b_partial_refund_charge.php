<?php

/**
 * Partially refund a charge by returning selected order items.
 *
 * The items and amounts must match items from the original charge. The refund
 * amount is calculated from the items' grossTotalAmount values.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-charges-chargeid-refunds-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\Item;
use NexiCheckout\Model\Request\PartialRefundCharge;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$chargeId = exampleEnv('NEXI_CHARGE_ID') ?? 'your-charge-id';
$idempotencyKey = exampleEnv('NEXI_IDEMPOTENCY_KEY');
$myReference = exampleEnv('NEXI_MY_REFERENCE');

// Replace these values with the exact item details from the original charge.
$refundItems = [
    new Item('Example item', 1, 'pcs', 5000, 5000, 5000, 'example-item-1'),
];

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Refund the selected order items
$refundResult = $paymentApi->refundCharge(
    $chargeId,
    new PartialRefundCharge($refundItems, $myReference),
    $idempotencyKey
);

exampleWriteLine('Charge partially refunded.', 'green');
exampleWriteLine('Charge ID: ' . $chargeId, 'yellow');
exampleWriteLine('Refund amount: ' . array_sum(array_map(
    static fn (Item $item): int => $item->getGrossTotalAmount(),
    $refundItems
)), 'yellow');
exampleWriteLine('Refund ID: ' . $refundResult->getRefundId(), 'yellow');

if ($idempotencyKey !== null) {
    exampleWriteLine('Idempotency key: ' . $idempotencyKey, 'yellow');
}

exampleWriteLine('Retrieve payment and verify refund status: 02_retrieve_payment.php');
