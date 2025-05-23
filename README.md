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

// Subscription related calls
$subscriptionApi = (new PaymentApiFactory($factory, $provider))->createSubscriptionApi($secretKey, $isLiveMode);

$subscriptionApi->retrieveSubscription('d079718b-ff63-45dd-947b-4950c023750f');
```

- Webhook models

```php
use NexiCheckout\Model\Webhook\WebhookBuilder;
use Psr\Http\Message\StreamInterface;

/** @var StreamInterface $request */
$payload = $request->getContents();
$result = WebhookBuilder::fromJson($payload);
```