<?php

declare(strict_types=1);

namespace NexiCheckout\Factory\Provider;

use NexiCheckout\Http\Configuration;

final class HttpClientConfigurationProvider implements HttpClientConfigurationProviderInterface
{
    private const LIVE_URL = 'https://api.dibspayment.eu';

    private const TEST_URL = 'https://test.api.dibspayment.eu';

    public function __construct(
        private readonly string $liveUrl = self::LIVE_URL,
        private readonly string $testUrl = self::TEST_URL,
        private ?string $commercePlatformTag = null
    ) {
    }

    public function provide(string $secretKey, bool $isLiveMode): Configuration
    {
        return new Configuration($secretKey, $this->baseUrl($isLiveMode), $this->commercePlatformTag);
    }

    public function setCommercePlatformTag(string $commercePlatformTag): void
    {
        $this->commercePlatformTag = $commercePlatformTag;
    }

    private function baseUrl(bool $isLiveMode): string
    {
        return $isLiveMode ? $this->liveUrl : $this->testUrl;
    }
}
