<?php

declare(strict_types=1);

namespace NexiCheckout\Http;

class UrlDictionary
{
    public const LIVE_CHECKOUT_URL = 'https://api.dibspayment.eu';

    public const TEST_CHECKOUT_URL = 'https://test.api.dibspayment.eu';

    public const LIVE_EMBEDDED_SDK = 'https://checkout.dibspayment.eu/v1/checkout.js?v=1';

    public const TEST_EMBEDDED_SDK = 'https://test.checkout.dibspayment.eu/v1/checkout.js?v=1';
}
