<?php

/**
 * Update the order for a payment.
 *
 * Changes the order on the checkout page after the payment object has been created.
 * This can only be used before checkout has been completed by the customer.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-orderitems-put
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\Item;
use NexiCheckout\Model\Request\UpdateOrder;
use NexiCheckout\Model\Request\UpdateOrder\PaymentMethod;
use NexiCheckout\Model\Request\UpdateOrder\Shipping;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Build updated order
$updatedItem = new Item('Updated example item', 1, 'pcs', 10000, 10000, 10000, 'updated-item-1');
$invoiceFee = new Item('Invoice fee', 1, 'pcs', 100, 100, 100, 'invoice-fee');

$updateOrderRequest = new UpdateOrder(
    10000,
    [$updatedItem],
    new Shipping(false),
    [
        new PaymentMethod('invoice', $invoiceFee),
    ]
);

// 3) Update payment order
$paymentApi->updatePaymentOrder($paymentId, $updateOrderRequest);

exampleWriteLine('Payment order updated.', 'green');
exampleWriteLine('Payment ID: ' . $paymentId, 'yellow');
exampleWriteLine('Updated order amount: 10000 SEK', 'yellow');
