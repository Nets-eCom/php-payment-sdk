<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Request\CardPayment;

final class CardDetails implements \JsonSerializable
{
    public function __construct(
        private readonly string $cardNumber,
        private readonly string $expiryMonth,
        private readonly string $expiryYear,
        private readonly string $cvc,
        private readonly ?string $cardHolderName = null,
        private readonly ?string $network = null,
    ) {
    }

    /**
     * @return array{
     *     cardNumber: string,
     *     expiryMonth: string,
     *     expiryYear: string,
     *     cvc: string,
     *     cardHolderName: ?string,
     *     network: ?string,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'cardNumber' => $this->cardNumber,
            'expiryMonth' => $this->expiryMonth,
            'expiryYear' => $this->expiryYear,
            'cvc' => $this->cvc,
            'cardHolderName' => $this->cardHolderName,
            'network' => $this->network,
        ];
    }
}
