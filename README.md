# Nexi Checkout PHP SDK

## Examples

- Setup using configuration provider & api factory

```php
use NexiCheckout\Factory\HttpClientFactory;
use NexiCheckout\Factory\PaymentApiFactory;
use NexiCheckout\Model\Request\Payment;
use NexiCheckout\Factory\Provider\HttpClientConfigurationProvider

$factory = new HttpClientFactory($psrClient, $psrFactory, $psrStreamFactory); 
$provider = new HttpClientConfigurationProvider();

$api = (new PaymentApiFactory($factory, $provider))->create($secretKey, $isLiveMode);

// Hosted checkout 
$hostedPayment = $api->createHostedPayment(new Payment(...));

// Embedded checkout  
$embeddedPayment = $api->createEmbeddedPayment(new Payment(...));
```
