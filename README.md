# Nexi Checkout PHP SDK

## Examples

- Setup using configuration provider & api factory

```php
use NexiCheckout\Factory\HttpClientFactory;
use NexiCheckout\Factory\PaymentApiFactory;
use NexiCheckout\Model\Request\Payment;
use NexiCheckout\Factory\Provider\HttpClientConfigurationProvider

$httpClientFactory = new HttpClientFactory($psrClient, $psrFactory, $psrStreamFactory); 
$httpClientConfigurationProvider = new HttpClientConfigurationProvider();

$api = (new PaymentApiFactory($httpClientFactory, $httpClientConfigurationProvider))
    ->create($secretKey, $isLiveMode);

$result = $api->createPayment(new Payment(...));
```
