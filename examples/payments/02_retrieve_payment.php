<?php

/**
 * Retrieve a payment by payment ID.
 *
 * Returns the current payment details, including status, order details, charges, and refunds.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-get
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$paymentId = exampleEnv('NEXI_PAYMENT_ID') ?? 'your-payment-id';

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Retrieve payment
$paymentResult = $paymentApi->retrievePayment($paymentId);
$payment = $paymentResult->getPayment();
$orderDetails = $payment->getOrderDetails();

exampleWriteLine('Payment retrieved.', 'green');
exampleWriteLine('Payment ID: ' . $payment->getPaymentId(), 'yellow');
exampleWriteLine('Status: ' . $payment->getStatus()->value, 'yellow');
exampleWriteLine('Amount: ' . $orderDetails->getAmount() . ' ' . $orderDetails->getCurrency(), 'yellow');
exampleWriteLine('Checkout URL: ' . $payment->getCheckout()->getUrl());

if ($orderDetails->getReference() !== null) {
    exampleWriteLine('Order reference: ' . $orderDetails->getReference());
}

if ($payment->getMyReference() !== null) {
    exampleWriteLine('My reference: ' . $payment->getMyReference());
}

if ($payment->getCharges() !== null) {
    exampleWriteLine('Charges on payment: ' . count($payment->getCharges()));
    foreach ($payment->getCharges() as $index => $charge) {
        exampleWriteLine('Charge #' . ($index + 1) . ' ID: ' . $charge->getChargeId());
    }
}

if ($payment->getRefunds() !== null) {
    exampleWriteLine('Refunds on payment: ' . count($payment->getRefunds()));
}

exampleWriteLine('Possible next steps:', 'cyan');
exampleWriteLine('1) If checkout is still open, update metadata: 03_update_reference_information.php, 04_update_order.php, or 05_update_my_reference.php');
exampleWriteLine('2) If payment is authorized, capture it: 08_charge_payment.php');
exampleWriteLine('3) If payment is captured, refund it: 09_refund_charge.php or 10_refund_payment.php');
