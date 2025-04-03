<?php

declare(strict_types=1);

namespace NexiCheckout\Tests\Api;

use NexiCheckout\Api\Exception\ClientErrorPaymentApiException;
use NexiCheckout\Api\Exception\PaymentApiException;
use NexiCheckout\Api\PaymentApi;
use NexiCheckout\Http\Configuration;
use NexiCheckout\Http\HttpClient;
use NexiCheckout\Model\Request\Charge;
use NexiCheckout\Model\Request\FullCharge;
use NexiCheckout\Model\Request\FullRefundCharge;
use NexiCheckout\Model\Request\Item;
use NexiCheckout\Model\Request\MyReference;
use NexiCheckout\Model\Request\Payment;
use NexiCheckout\Model\Request\Payment\HostedCheckout;
use NexiCheckout\Model\Request\ReferenceInformation;
use NexiCheckout\Model\Request\RefundCharge;
use NexiCheckout\Model\Request\RefundPayment;
use NexiCheckout\Model\Request\Shared\Notification;
use NexiCheckout\Model\Request\Shared\Notification\Webhook;
use NexiCheckout\Model\Request\Shared\Order;
use NexiCheckout\Model\Request\UpdateOrder;
use NexiCheckout\Model\Request\UpdateOrder\PaymentMethod;
use NexiCheckout\Model\Request\UpdateOrder\Shipping;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class PaymentApiTest extends TestCase
{
    public function testItCreatesHostedPayment(): void
    {
        $response = $this->createResponse([
            'paymentId' => '1234',
            'hostedPaymentPageUrl' => 'https://api.example.com/hostedUrl',
        ], 200);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->createHostedPayment($this->createPaymentRequest());

        $this->assertSame('1234', $result->getPaymentId());
        $this->assertSame('https://api.example.com/hostedUrl', $result->getHostedPaymentPageUrl());
    }

    public function testItCreatesEmbeddedPayment(): void
    {
        $response = $this->createResponse([
            'paymentId' => '1234',
        ], 200);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->createEmbeddedPayment($this->createPaymentRequest());

        $this->assertSame('1234', $result->getPaymentId());
    }

    public function testItThrowsExceptionOnPsrClientExceptionCreatePayment(): void
    {
        $this->expectException(PaymentApiException::class);

        $sut = new PaymentApi(
            new HttpClient(
                $this->createPsrClientThrowingException(),
                $this->createRequestFactoryStub(),
                $this->createStub(StreamFactoryInterface::class),
                new Configuration('1234', 'https://api.example.com')
            ),
        );

        $sut->createHostedPayment($this->createPaymentRequest());
    }

    public function testItThrowsExceptionOnClientErrorCreatePayment(): void
    {
        $this->expectException(ClientErrorPaymentApiException::class);

        $response = $this->createResponse([
            'errors' => [
                'property1' => ['string'],
                'property2' => ['string'],
            ],
        ], 400);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));
        $sut->createHostedPayment($this->createPaymentRequest());
    }

    public function testItThrowsExceptionOnUnsuccessfulCreatePayment(): void
    {
        $this->expectException(PaymentApiException::class);

        $response = $this->createResponse([], 500);

        $sut = $this->createPaymentApi($response, $this->createStub(StreamFactoryInterface::class));
        $sut->createHostedPayment($this->createPaymentRequest());
    }

    public function testItRetrievesPayment(): void
    {
        $response = $this->createResponse([
            'payment' => [
                'paymentId' => '1234',
                'orderDetails' => [
                    'amount' => 1000,
                    'currency' => 'PLN',
                ],
                'created' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
                'consumer' => [
                    'shippingAddress' => [],
                    'billingAddress' => [],
                    'privatePerson' => [],
                    'company' => [],
                ],
                'summary' => [],
                'paymentDetails' => [
                    'invoiceDetails' => [],
                ],
                'checkout' => [
                    'url' => 'https://shop.example.com/checkout/1000',
                    'cancelUrl' => 'https://shop.example.com/cancelUrl',
                ],
            ],
        ], 200);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrievePayment('1234');

        $payment = $result->getPayment();

        $this->assertSame($payment->getPaymentId(), '1234');
        $this->assertFalse($payment->isSubscriptionPayment());
    }

    public function testItThrowsExceptionOnUnknownPaymentRetrieve(): void
    {
        $this->expectException(PaymentApiException::class);

        $response = $this->createResponse([], 404);

        $sut = $this->createPaymentApi($response, $this->createStub(StreamFactoryInterface::class));

        $sut->retrievePayment('1234');
    }

    public function testItCreatesCharge(): void
    {
        $response = $this->createResponse([
            'chargeId' => '1234',
        ], 200);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->charge('1234', $this->createChargeRequest());

        $this->assertSame('1234', $result->getChargeId());
    }

    public function testItUpdatesReferenceInformation(): void
    {
        $response = $this->createResponse([], 200);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));

        $sut->updateReferenceInformation('1234', $this->createReferenceInformationRequest());
    }

    public function testItUpdatesMyReferenceInformation(): void
    {
        $response = $this->createResponse([], 204);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));

        $sut->updateMyReference('1234', $this->createMyReferenceRequest());
    }

    public function testItUpdatesOrder(): void
    {
        $response = $this->createResponse([], 204);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));
        $sut->updatePaymentOrder('1234', $this->createUpdateOrderRequest());
    }

    public function testItTerminatesPayment(): void
    {
        $response = $this->createResponse([], 200);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));

        $sut->terminate('1234');
    }

    public function testItRefundsCharge(): void
    {
        $response = $this->createResponse([
            'refundId' => '1234',
        ], 200);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->refundCharge('1234', $this->createRefundChargeRequest());

        $this->assertSame('1234', $result->getRefundId());
    }

    public function testItRefundsPayment(): void
    {
        $response = $this->createResponse([
            'refundId' => '1234',
        ], 200);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->refundPayment('1234', $this->createRefundPaymentRequest());

        $this->assertSame('1234', $result->getRefundId());
    }

    public function testItCancelsPendingRefund(): void
    {
        $response = $this->createResponse([], 204);

        $sut = $this->createPaymentApi($response, $this->createStreamFactory($response->getBody()));

        $sut->cancelPendingRefund('1234');
    }

    /**
     * @param array<string, mixed> $data
     */
    private function createResponse(array $data, int $code): ResponseInterface
    {
        $contents = $data !== [] ? json_encode($data) : '';

        $stream = $this->createStub(StreamInterface::class);
        $stream->method('getContents')->willReturn($contents);

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('getStatusCode')->willReturn($code);
        $response->method('getBody')->willReturn($stream);

        return $response;
    }

    private function createPsrClient(ResponseInterface $response): ClientInterface
    {
        return new class($response) implements ClientInterface {
            public function __construct(private readonly ResponseInterface $response)
            {
            }

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return $this->response;
            }
        };
    }

    private function createPsrClientThrowingException(): ClientInterface
    {
        return new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                throw $this->createClientException();
            }

            private function createClientException(): ClientExceptionInterface
            {
                return new class extends \Exception implements ClientExceptionInterface {};
            }
        };
    }

    private function createPaymentRequest(): Payment
    {
        return new Payment(
            new Order(
                [
                    new Item(
                        'item',
                        1,
                        'pcs',
                        100,
                        100,
                        100,
                        'ref'
                    ),
                ],
                'SEK',
                100
            ),
            new HostedCheckout(
                'https://shop.example.com/returnUrl',
                'https://shop.example.com/cancelUrl',
                'https://shop.example.com/termsUrl',
            ),
            new Notification(
                [
                    new Webhook('event', 'https://shop.example.com', '1234'),
                ]
            )
        );
    }

    private function createChargeRequest(): Charge
    {
        return new FullCharge(1);
    }

    private function createReferenceInformationRequest(): ReferenceInformation
    {
        return new ReferenceInformation('https://shop.example.com/checkout/1000', 'ref1234');
    }

    private function createMyReferenceRequest(): MyReference
    {
        return new MyReference('foo');
    }

    private function createRefundChargeRequest(): RefundCharge
    {
        return new FullRefundCharge(1);
    }

    private function createUpdateOrderRequest(): UpdateOrder
    {
        $item = new Item('bar', 1, 'pcs', 1, 1, 1, 'foo');

        return new UpdateOrder(
            1,
            [$item],
            new Shipping(false),
            [
                new PaymentMethod(
                    'foo',
                    $item
                ),
            ]
        );
    }

    private function createRefundPaymentRequest(): RefundPayment
    {
        return new RefundPayment(1);
    }

    private function createRequestFactoryStub(): RequestFactoryInterface
    {
        $request = $this->createStub(RequestInterface::class);
        $request->method('withBody')->willReturnSelf();
        $request->method('withHeader')->willReturnSelf();

        $requestFactory = $this->createStub(RequestFactoryInterface::class);
        $requestFactory->method('createRequest')->willReturn($request);

        return $requestFactory;
    }

    private function createPaymentApi(ResponseInterface $response, StreamFactoryInterface $streamFactory): PaymentApi
    {
        return new PaymentApi(
            new HttpClient(
                $this->createPsrClient($response),
                $this->createRequestFactoryStub(),
                $streamFactory,
                new Configuration('1234', 'https://api.example.com')
            )
        );
    }

    private function createStreamFactory(StreamInterface $stream): StreamFactoryInterface
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->willReturn($stream);

        return $streamFactory;
    }
}
