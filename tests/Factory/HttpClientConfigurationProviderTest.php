<?php

declare(strict_types=1);

namespace NexiCheckout\Tests\Factory;

use NexiCheckout\Factory\Provider\HttpClientConfigurationProvider;
use NexiCheckout\Http\Configuration;
use PHPUnit\Framework\TestCase;

final class HttpClientConfigurationProviderTest extends TestCase
{
    public function testItProvidesDefaultLiveConfiguration(): void
    {
        $sut = new HttpClientConfigurationProvider();

        $result = $sut->provide('foo', true);

        $this->assertResult($result, 'foo', 'https://api.dibspayment.eu');
    }

    public function testItProvidesDefaultTestConfiguration(): void
    {
        $sut = new HttpClientConfigurationProvider();

        $result = $sut->provide('foo', false);

        $this->assertResult($result, 'foo', 'https://test.api.dibspayment.eu');
    }

    public function testItProvidesCustomLiveConfiguration(): void
    {
        $liveUrl = 'https://custom-live.example.com';
        $testUrl = 'https://custom-test.example.com';
        $commercePlatformTag = 'tag';
        $secretKey = 'foo';

        $sut = new HttpClientConfigurationProvider(
            $liveUrl,
            $testUrl,
            $commercePlatformTag
        );

        $result = $sut->provide($secretKey, true);

        $this->assertResult($result, $secretKey, $liveUrl, $commercePlatformTag);
    }

    public function testItProvidesCustomTestConfiguration(): void
    {
        $liveUrl = 'https://custom-live.example.com';
        $testUrl = 'https://custom-test.example.com';
        $commercePlatformTag = 'tag';
        $secretKey = 'foo';

        $sut = new HttpClientConfigurationProvider(
            $liveUrl,
            $testUrl,
            $commercePlatformTag
        );

        $result = $sut->provide($secretKey, false);

        $this->assertResult($result, $secretKey, $testUrl, $commercePlatformTag);
    }

    public function testItAllowSetterInjectionOfCommerceTag(): void
    {
        $liveUrl = 'https://custom-live.example.com';
        $testUrl = 'https://custom-test.example.com';
        $commercePlatformTag = 'tag';
        $secretKey = 'foo';

        $sut = new HttpClientConfigurationProvider(
            $liveUrl,
            $testUrl,
        );
        $sut->setCommercePlatformTag($commercePlatformTag);

        $result = $sut->provide($secretKey, false);

        $this->assertResult($result, $secretKey, $testUrl, $commercePlatformTag);
    }

    private function assertResult(
        Configuration $result,
        string $secretKey,
        string $baseUrl,
        ?string $commercePlatformTag = null
    ): void {
        $this->assertInstanceOf(Configuration::class, $result);
        $this->assertSame($secretKey, $result->getSecretKey());
        $this->assertSame($baseUrl, $result->getBaseUrl());
        $this->assertSame($commercePlatformTag, $result->getCommercePlatformTag());
    }
}
