<?php

declare(strict_types=1);

namespace NexiCheckout\Factory;

use NexiCheckout\Api\PaymentApi;
use NexiCheckout\Factory\Provider\HttpClientConfigurationProviderInterface;

class PaymentApiFactory
{
    public function __construct(
        private readonly HttpClientFactory $clientFactory,
        private readonly HttpClientConfigurationProviderInterface $configurationProvider,
    ) {
    }

    public function create(
        string $secretKey,
        bool $isLiveMode
    ): PaymentApi {
        return new PaymentApi(
            $this->clientFactory->create(
                $this->configurationProvider->provide($secretKey, $isLiveMode)
            )
        );
    }
}
