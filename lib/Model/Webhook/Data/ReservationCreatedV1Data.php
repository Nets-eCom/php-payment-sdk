<?php declare(strict_types=1);

namespace NexiCheckout\Model\Webhook\Data;

use NexiCheckout\Model\Webhook\Shared\Data;

class ReservationCreatedV1Data extends Data
{
    public function __construct(
        string $paymentId,
        private readonly string $paymentMethod,
        private readonly string $paymentType,
        private readonly Consumer $consumer,
        private readonly Amount $amount,
        private readonly ?CardDetails $cardDetails = null,
        private readonly ?string $myReference = null,
        private readonly ?string $reserveId = null,
        private readonly ?string $reservationReference = null,
        private readonly ?string $reconciliationReference = null,
    ) {
        parent::__construct($paymentId);
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getPaymentType(): string
    {
        return $this->paymentType;
    }

    public function getConsumer(): Consumer
    {
        return $this->consumer;
    }

    public function getAmount(): Amount
    {
        return $this->amount;
    }

    public function getCardDetails(): ?CardDetails
    {
        return $this->cardDetails;
    }

    public function getMyReference(): ?string
    {
        return $this->myReference;
    }

    public function getReserveId(): ?string
    {
        return $this->reserveId;
    }

    public function getReservationReference(): ?string
    {
        return $this->reservationReference;
    }

    public function getReconciliationReference(): ?string
    {
        return $this->reconciliationReference;
    }
}
