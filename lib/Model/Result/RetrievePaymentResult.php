<?php

declare(strict_types=1);

namespace NexiCheckout\Model\Result;

use NexiCheckout\Model\Result\RetrievePayment\Charge;
use NexiCheckout\Model\Result\RetrievePayment\Checkout;
use NexiCheckout\Model\Result\RetrievePayment\Company\ContactDetails;
use NexiCheckout\Model\Result\RetrievePayment\Consumer;
use NexiCheckout\Model\Result\RetrievePayment\Consumer\Address;
use NexiCheckout\Model\Result\RetrievePayment\Consumer\Company;
use NexiCheckout\Model\Result\RetrievePayment\Consumer\PrivatePerson;
use NexiCheckout\Model\Result\RetrievePayment\Item;
use NexiCheckout\Model\Result\RetrievePayment\OrderDetails;
use NexiCheckout\Model\Result\RetrievePayment\Payment;
use NexiCheckout\Model\Result\RetrievePayment\PaymentDetails;
use NexiCheckout\Model\Result\RetrievePayment\PaymentDetails\InvoiceDetails;
use NexiCheckout\Model\Result\RetrievePayment\Refund;
use NexiCheckout\Model\Result\RetrievePayment\RefundStateEnum;
use NexiCheckout\Model\Result\RetrievePayment\Subscription;
use NexiCheckout\Model\Result\RetrievePayment\Summary;
use NexiCheckout\Model\Result\RetrievePayment\UnscheduledSubscription;
use NexiCheckout\Model\Result\Shared\CardDetails;
use NexiCheckout\Model\Result\Shared\PaymentTypeEnum;
use NexiCheckout\Model\Result\Shared\PhoneNumber;
use NexiCheckout\Model\Shared\JsonDeserializeInterface;
use NexiCheckout\Model\Shared\JsonDeserializeTrait;

class RetrievePaymentResult implements JsonDeserializeInterface
{
    use JsonDeserializeTrait;

    public function __construct(private readonly Payment $payment)
    {
    }

    /**
     * @param array{
     *     url: string,
     *     cancelUrl: ?string
     * } $data
     */
    public static function createCheckout(array $data): Checkout
    {
        return new Checkout(
            $data['url'],
            $data['cancelUrl'] ?? null
        );
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    /**
     * @throws \Exception
     */
    public static function fromJson(string $string): RetrievePaymentResult
    {
        $payment = self::jsonDeserialize($string)['payment'];

        return new self(
            self::createPayment($payment)
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    private static function createPayment(array $data): Payment
    {
        return new Payment(
            $data['paymentId'],
            new OrderDetails(...$data['orderDetails']),
            self::createCheckout($data['checkout']),
            new \DateTimeImmutable($data['created']),
            self::createConsumer($data['consumer']),
            isset($data['terminated']) ? new \DateTimeImmutable($data['terminated']) : null,
            isset($data['summary']) ? self::createSummary($data['summary']) : null,
            isset($data['paymentDetails']) ? self::createPaymentDetails($data['paymentDetails']) : null,
            isset($data['refunds']) ? self::createRefunds($data['refunds']) : null,
            isset($data['charges']) ? self::createCharges($data['charges']) : null,
            isset($data['subscription']) ? self::createSubscription($data['subscription']) : null,
            isset($data['unscheduledSubscription']) ? self::createUnscheduledSubscription($data['unscheduledSubscription']) : null,
            $data['myReference'] ?? null
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createConsumer(array $data): Consumer
    {
        return new Consumer(
            self::createAddress($data['shippingAddress']),
            self::createAddress($data['billingAddress']),
            self::createPrivatePerson($data['privatePerson']),
            self::createCompany($data['company'])
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createAddress(array $data): Address
    {
        return new Address(
            $data['addressLine1'] ?? null,
            $data['addressLine2'] ?? null,
            $data['receiverLine'] ?? null,
            $data['postalCode'] ?? null,
            $data['city'] ?? null,
            self::createPhoneNumber($data['phoneNumber'] ?? [])
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createCompany(array $data): Company
    {
        return new Company(
            $data['merchantReference'] ?? null,
            $data['name'] ?? null,
            $data['registrationNumber'] ?? null,
            isset($data['contactDetails']) ? self::createContactDetails($data['contactDetails']) : null
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    private static function createPrivatePerson(array $data): PrivatePerson
    {
        return new PrivatePerson(
            $data['merchantReference'] ?? null,
            isset($data['dateOfBirth']) ? new \DateTime($data['dateOfBirth']) : null,
            $data['firstName'] ?? null,
            $data['lastName'] ?? null,
            $data['email'] ?? null,
            self::createPhoneNumber($data['phoneNumber'] ?? [])
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createContactDetails(array $data): ContactDetails
    {
        return new ContactDetails(
            $data['firstName'] ?? null,
            $data['lastName'] ?? null,
            $data['email'] ?? null,
            self::createPhoneNumber($data['phoneNumber'] ?? [])
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createSummary(array $data): Summary
    {
        return new Summary(
            $data['reservedAmount'] ?? 0,
            $data['chargedAmount'] ?? 0,
            $data['refundedAmount'] ?? 0,
            $data['cancelledAmount'] ?? 0
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createPaymentDetails(array $data): PaymentDetails
    {
        return new PaymentDetails(
            isset($data['paymentType']) ? PaymentTypeEnum::tryFrom($data['paymentType']) : null,
            $data['paymentMethod'] ?? null,
            isset($data['invoiceDetails']) ? self::createInvoiceDetails($data['invoiceDetails']) : null,
            isset($data['cardDetails']) ? self::createCardDetails($data['cardDetails']) : null
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<Refund>
     */
    private static function createRefunds(array $data): array
    {
        $result = [];
        foreach ($data as $refund) {
            $result[] = self::createRefund($refund);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    private static function createRefund(array $data): Refund
    {
        return new Refund(
            $data['refundId'],
            $data['amount'],
            RefundStateEnum::tryFrom($data['state']),
            new \DateTime($data['lastUpdated']),
            self::createOrderItems($data['orderItems'])
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<Charge>
     */
    private static function createCharges(array $data): array
    {
        $result = [];
        foreach ($data as $charge) {
            $result[] = self::createCharge($charge);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws \Exception
     */
    private static function createCharge(array $data): Charge
    {
        return new Charge(
            $data['chargeId'],
            $data['amount'],
            new \DateTime($data['created']),
            self::createOrderItems($data['orderItems'])
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array<Item>
     */
    private static function createOrderItems(array $data): array
    {
        $result = [];
        foreach ($data as $item) {
            $result[] = self::createItem($item);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createItem(array $data): Item
    {
        return new Item(
            $data['name'],
            $data['quantity'],
            $data['unit'],
            $data['unitPrice'],
            $data['grossTotalAmount'],
            $data['netTotalAmount'],
            $data['reference'],
            $data['taxRate'] ?? null,
            $data['taxAmount'] ?? null
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createInvoiceDetails(array $data): ?InvoiceDetails
    {
        if ($data === []) {
            return null;
        }

        return new InvoiceDetails(
            $data['invoiceNumber'] ?? null
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createCardDetails(array $data): ?CardDetails
    {
        if ($data === []) {
            return null;
        }

        return new CardDetails(
            $data['maskedPan'] ?? null,
            $data['expirationDate'] ?? null
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function createPhoneNumber(array $data): ?PhoneNumber
    {
        if ($data === []) {
            return null;
        }

        return new PhoneNumber(
            $data['prefix'],
            $data['number']
        );
    }

    /**
     * @param array<string, string> $data
     */
    private static function createSubscription(array $data): Subscription
    {
        return new Subscription($data['id']);
    }

    /**
     * @param array<string, string> $data
     */
    private static function createUnscheduledSubscription(array $data): UnscheduledSubscription
    {
        return new UnscheduledSubscription($data['unscheduledSubscriptionId']);
    }
}
