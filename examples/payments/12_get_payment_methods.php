<?php

/**
 * Get payment methods for a merchant.
 *
 * Retrieves available payment methods, optionally filtered by merchant number, currency, and enabled status.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-paymentmethods-get
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

use NexiCheckout\Model\Request\PaymentMethods;

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$currency = exampleEnv('NEXI_CURRENCY') ?? 'SEK';
$enabled = exampleEnv('NEXI_ENABLED') !== 'false';
$merchantNumber = exampleEnv('NEXI_MERCHANT_NUMBER');

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Retrieve payment methods
$paymentMethods = $paymentApi->getPaymentMethods(
    new PaymentMethods($currency, $enabled, $merchantNumber)
);

exampleWriteLine('Payment methods retrieved.', 'green');
exampleWriteLine('Requested currency: ' . $currency, 'yellow');
exampleWriteLine('Enabled only: ' . ($enabled ? 'yes' : 'no'), 'yellow');

foreach ($paymentMethods->getMethods() as $method) {
    exampleWriteLine(
        sprintf(
            '%s | type=%s | currency=%s | enabled=%s',
            $method->getName() ?? 'unknown',
            $method->getPaymentType() ?? 'unknown',
            $method->getCurrency() ?? 'any',
            $method->isEnabled() ? 'yes' : 'no'
        )
    );
}
