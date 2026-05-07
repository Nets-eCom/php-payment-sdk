<?php

declare(strict_types=1);

namespace NexiCheckout\Factory\Provider;

use NexiCheckout\Http\Configuration;
use NexiCheckout\Http\UrlDictionary;

final class HttpClientConfigurationProvider implements HttpClientConfigurationProviderInterface
{
    public function __construct(
        private readonly string $liveUrl = UrlDictionary::LIVE_CHECKOUT_URL,
        private readonly string $testUrl = UrlDictionary::TEST_CHECKOUT_URL,
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
