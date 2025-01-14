<?php

declare(strict_types=1);

namespace NexiCheckout\Factory\Provider;

use NexiCheckout\Http\Configuration;

interface HttpClientConfigurationProviderInterface
{
    public function provide(string $secretKey, bool $isLiveMode): Configuration;
}
