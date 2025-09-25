<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result\PaymentMethodsResult;

use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

final class PaymentMethodsResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    /**
     * @param list<PaymentMethod> $methods
     */
    public function __construct(
        private readonly array $methods
    ) {
    }

    /**
     * @return list<PaymentMethod>
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    public static function fromJson(string $string): PaymentMethodsResult
    {
        $data = self::jsonDeserialize($string);

        $methods = [];
        foreach ($data as $row) {
            if (\is_array($row)) {
                $methods[] = new PaymentMethod(
                    $row['name'] ?? null,
                    $row['paymentType'] ?? null,
                    $row['currency'] ?? null,
                    (bool) ($row['enabled'] ?? false)
                );
            }
        }

        return new self($methods);
    }
}
