<?php

declare(strict_types=1);

use NexiCheckout\Factory\HttpClientFactory;
use NexiCheckout\Factory\PaymentApiFactory;
use NexiCheckout\Factory\Provider\HttpClientConfigurationProvider;
use NexiCheckout\Model\Request\Item;
use NexiCheckout\Model\Request\Shared\Order;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Component\HttpClient\Psr18Client;

function exampleCreatePaymentApiFactory(): PaymentApiFactory
{
    $psr17Factory = new Psr17Factory();

    return new PaymentApiFactory(
        new HttpClientFactory(new Psr18Client(), $psr17Factory, $psr17Factory),
        new HttpClientConfigurationProvider()
    );
}

function exampleCreateOrder(string $reference = 'example-order'): Order
{
    $item = new Item('Example item', 1, 'pcs', 10000, 10000, 10000, 'example-item-1');
    return new Order([$item], 'SEK', 10000, $reference);
}

function exampleEnv(string $name): ?string
{
    static $loaded = false;

    if ($loaded === false) {
        $loaded = true;
        $envFile = __DIR__ . '/.env';

        if (is_readable($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            if (is_array($lines)) {
                foreach ($lines as $line) {
                    $trimmedLine = trim($line);

                    if ($trimmedLine === '' || str_starts_with($trimmedLine, '#')) {
                        continue;
                    }

                    $separatorPosition = strpos($trimmedLine, '=');

                    if ($separatorPosition === false) {
                        continue;
                    }

                    $key = trim(substr($trimmedLine, 0, $separatorPosition));
                    $value = trim(substr($trimmedLine, $separatorPosition + 1));

                    if ($key === '') {
                        continue;
                    }

                    if (
                        strlen($value) >= 2
                        && (($value[0] === '"' && $value[strlen($value) - 1] === '"') || ($value[0] === '\'' && $value[strlen($value) - 1] === '\''))
                    ) {
                        $value = substr($value, 1, -1);
                    }

                    if (getenv($key) === false) {
                        putenv($key . '=' . $value);
                        $_ENV[$key] = $value;
                    }
                }
            }
        }
    }

    $value = getenv($name);

    if ($value === false) {
        return null;
    }

    $value = trim($value);

    return $value === '' ? null : $value;
}

/**
 * @return list<string>|null
 */
function exampleEnvList(string $name): ?array
{
    $value = exampleEnv($name);

    if ($value === null) {
        return null;
    }

    $values = array_map('trim', explode(',', $value));
    $values = array_filter($values, static fn (string $value): bool => $value !== '');

    return array_values($values);
}

function exampleWriteLine(string $message, string $colour = ''): void
{
    if ($colour === '') {
        fwrite(STDOUT, $message . PHP_EOL);

        return;
    }

    $colours = [
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
    ];

    $colourCode = $colours[$colour] ?? '';
    $resetCode = $colourCode === '' ? '' : "\033[0m";

    fwrite(STDOUT, $colourCode . $message . $resetCode . PHP_EOL);
}
