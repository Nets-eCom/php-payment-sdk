<?php

/**
 * Payment happy path.
 *
 * First run: creates a hosted payment and prints the checkout URL.
 * Second run: after checkout is completed, retrieves the payment and captures it.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-post
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-get
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-payments-paymentid-charges-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\FullCharge;
use NexiCheckout\Model\Request\Payment;
use NexiCheckout\Model\Request\Payment\HostedCheckout;
use NexiCheckout\Model\Request\Payment\MethodConfiguration;
use NexiCheckout\Model\Result\RetrievePayment\PaymentStatusEnum;

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

if ($paymentId === 'your-payment-id') {
    // 2) Create hosted payment when no payment ID is available yet
    $hostedPayment = $paymentApi->createHostedPayment(createHostedPaymentRequest());

    exampleWriteLine('Hosted payment created. Open the checkout URL and complete the payment, then rerun this file with NEXI_PAYMENT_ID set to the value below.', 'green');
    exampleWriteLine($hostedPayment->getHostedPaymentPageUrl());
    exampleWriteLine('Payment ID: ' . $hostedPayment->getPaymentId(), 'yellow');

    exit(0);
}

// 3) Retrieve the completed payment
$paymentResult = $paymentApi->retrievePayment($paymentId);
$payment = $paymentResult->getPayment();
$orderDetails = $payment->getOrderDetails();

exampleWriteLine('Payment retrieved.', 'green');
exampleWriteLine('Payment ID: ' . $payment->getPaymentId(), 'yellow');
exampleWriteLine('Status: ' . $payment->getStatus()->value, 'yellow');
exampleWriteLine('Amount: ' . $orderDetails->getAmount() . ' ' . $orderDetails->getCurrency(), 'yellow');

$charges = $payment->getCharges() ?? [];

if ($charges !== []) {
    exampleWriteLine('This payment already has charge(s), so the happy path is complete.', 'cyan');

    foreach ($charges as $index => $charge) {
        exampleWriteLine('Charge #' . ($index + 1) . ' ID: ' . $charge->getChargeId(), 'yellow');
    }

    exit(0);
}

if ($payment->getStatus() !== PaymentStatusEnum::RESERVED) {
    exampleWriteLine('The payment is not ready to charge yet. Complete checkout first, then rerun once the payment status becomes reserved.', 'red');
    exit(1);
}

// 4) Charge the reserved payment
$effectiveIdempotencyKey = $idempotencyKey ?? 'payment-happy-path-' . $paymentId;
$chargeResult = $paymentApi->charge(
    $paymentId,
    new FullCharge($chargeAmount, null, $myReference),
    $effectiveIdempotencyKey
);

exampleWriteLine('Payment charged.', 'green');
exampleWriteLine('Charge amount: ' . $chargeAmount, 'yellow');
exampleWriteLine('Charge ID: ' . $chargeResult->getChargeId(), 'yellow');
exampleWriteLine('Idempotency key: ' . $effectiveIdempotencyKey, 'yellow');

function createHostedPaymentRequest(): Payment
{
    $exampleOrder = exampleCreateOrder('payment-happy-path-order');
    // Optional env overrides; fallback URLs are demo placeholders.
    $returnUrl = exampleEnv('NEXI_RETURN_URL') ?? 'https://shop.example.com/returnUrl';
    $cancelUrl = exampleEnv('NEXI_CANCEL_URL') ?? 'https://shop.example.com/cancelUrl';
    $termsUrl = exampleEnv('NEXI_TERMS_URL') ?? 'https://shop.example.com/termsUrl';

    return new Payment(
        $exampleOrder,
        new HostedCheckout(
            $returnUrl,
            $cancelUrl,
            $termsUrl
        ),
        null,
        null,
        null,
        null,
        'sdk-example-' . bin2hex(random_bytes(4)),
        [
            new MethodConfiguration('card', true),
        ]
    );
}
