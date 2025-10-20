<?php

declare(strict_types=1);

namespace Model\Payment\Result\RetrievePayment;

use NexiCheckout\Model\Result\RetrievePayment\Checkout;
use NexiCheckout\Model\Result\RetrievePayment\Consumer;
use NexiCheckout\Model\Result\RetrievePayment\Consumer\Address;
use NexiCheckout\Model\Result\RetrievePayment\Consumer\Company;
use NexiCheckout\Model\Result\RetrievePayment\Consumer\PrivatePerson;
use NexiCheckout\Model\Result\RetrievePayment\OrderDetails;
use NexiCheckout\Model\Result\RetrievePayment\Payment;
use NexiCheckout\Model\Result\RetrievePayment\PaymentStatusEnum;
use NexiCheckout\Model\Result\RetrievePayment\Summary;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PaymentTest extends TestCase
{
    #[DataProvider('summaryProvider')]
    public function testItDerivesStatusFromSummary(
        Summary $summary,
        PaymentStatusEnum $statusEnum,
        ?\DateTimeInterface $terminationDate
    ): void {
        $sut = new Payment(
            'foo',
            new OrderDetails(1, 'bar'),
            new Checkout('baz', null),
            new \DateTimeImmutable(),
            new Consumer(
                $this->createStub(Address::class),
                $this->createStub(Address::class),
                $this->createStub(PrivatePerson::class),
                $this->createStub(Company::class),
            ),
            $terminationDate,
            $summary
        );

        $this->assertSame($statusEnum, $sut->getStatus());
    }

    /**
     * @return iterable<array{Summary, PaymentStatusEnum, ?\DateTimeInterface}>
     */
    public static function summaryProvider(): iterable
    {
        yield [new Summary(1, 0, 0, 0, 0, 0, 0, 0), PaymentStatusEnum::RESERVED, null];
        yield [new Summary(2, 2, 0, 0, 0, 0, 0, 0), PaymentStatusEnum::CHARGED, null];
        yield [new Summary(2, 1, 0, 0, 0, 0, 0, 0), PaymentStatusEnum::PARTIALLY_CHARGED, null];
        yield [new Summary(2, 2, 2, 0, 0, 0, 0, 0), PaymentStatusEnum::REFUNDED, null];
        yield [new Summary(2, 2, 1, 0, 0, 0, 0, 0), PaymentStatusEnum::PARTIALLY_REFUNDED, null];
        yield [new Summary(2, 0, 0, 2, 0, 0, 0, 0), PaymentStatusEnum::CANCELLED, null];
        yield [new Summary(0, 0, 0, 0, 0, 0, 0, 0), PaymentStatusEnum::TERMINATED, new \DateTimeImmutable()];
    }
}
