<?php

/**
 * Cancel a pending refund.
 *
 * Cancels a refund that is still pending, for example when there are not enough funds in the merchant account.
 *
 * Endpoint documentation:
 * https://developer.nexigroup.com/nexi-checkout/en-EU/api/payment-v1/#v1-pending-refunds-refundid-cancel-post
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../functions.php';

// You can override these values with env vars; fallback values are examples only.
$secretKey = exampleEnv('NEXI_SECRET_KEY') ?? 'your-test-secret-key';
$liveMode = exampleEnv('NEXI_LIVE_MODE') === 'true';
$refundId = exampleEnv('NEXI_REFUND_ID') ?? 'your-refund-id';

// 1) Create payment API client
$paymentApiFactory = exampleCreatePaymentApiFactory();
$paymentApi = $paymentApiFactory->create($secretKey, $liveMode);

// 2) Cancel pending refund
$paymentApi->cancelPendingRefund($refundId);

exampleWriteLine('Pending refund cancelled.', 'green');
exampleWriteLine('Refund ID: ' . $refundId, 'yellow');
