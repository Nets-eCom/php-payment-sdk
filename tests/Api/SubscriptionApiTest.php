<?php

declare(strict_types=1);

namespace NexiCheckout\Tests\Api;

use NexiCheckout\Api\Exception\ClientErrorPaymentApiException;
use NexiCheckout\Api\Exception\PaymentApiException;
use NexiCheckout\Api\SubscriptionApi;
use NexiCheckout\Http\Configuration;
use NexiCheckout\Http\HttpClient;
use NexiCheckout\Http\HttpClientException;
use NexiCheckout\Model\Request\BulkChargeSubscription;
use NexiCheckout\Model\Request\ChargeSubscription;
use NexiCheckout\Model\Request\ChargeUnscheduledSubscription;
use NexiCheckout\Model\Request\Item;
use NexiCheckout\Model\Request\Shared\Notification;
use NexiCheckout\Model\Request\Shared\Notification\Webhook;
use NexiCheckout\Model\Request\Shared\Order;
use NexiCheckout\Model\Request\VerifySubscriptions;
use NexiCheckout\Model\Request\VerifySubscriptions\Subscription;
use NexiCheckout\Model\Result\Shared\BulkOperationStatusEnum;
use NexiCheckout\Model\Result\Shared\VerificationStatusEnum;
use NexiCheckout\Model\Result\SubscriptionCharges\ChargeStatusEnum;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;

final class SubscriptionApiTest extends TestCase
{
    public function testItRetrievesSubscription(): void
    {
        $subscriptionId = 'foo';
        $response = $this->createResponse(
            [
                'subscriptionId' => $subscriptionId,
                'interval' => 0,
                'endDate' => '2019-08-24T14:15:22Z',
                'paymentDetails' => [
                    'paymentType' => 'CARD',
                    'paymentMethod' => 'Visa',
                    'cardDetails' => [
                        'expiryDate' => 'foo',
                        'maskedPan' => 'bar',
                    ],
                ],
            ],
            200
        );

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveSubscription($subscriptionId);

        $this->assertSame($subscriptionId, $result->getSubscriptionId());
    }

    public function testItRetrievesSubscriptionByExternalReference(): void
    {
        $subscriptionId = 'foo';
        $response = $this->createResponse(
            [
                'subscriptionId' => $subscriptionId,
                'interval' => 0,
                'endDate' => '2019-08-24T14:15:22Z',
                'paymentDetails' => [
                    'paymentType' => 'CARD',
                    'paymentMethod' => 'Visa',
                    'cardDetails' => [
                        'expiryDate' => 'foo',
                        'maskedPan' => 'bar',
                    ],
                ],
            ],
            200
        );

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveSubscriptionByExternalReference($subscriptionId, 'ref');

        $this->assertSame($subscriptionId, $result->getSubscriptionId());
    }

    public function testItRetrievesUnscheduledSubscription(): void
    {
        $unscheduledSubscriptionId = 'abc-123';
        $response = $this->createResponse(
            [
                'unscheduledSubscriptionId' => $unscheduledSubscriptionId,
                'paymentDetails' => [
                    'paymentType' => 'CARD',
                    'paymentMethod' => 'Visa',
                    'cardDetails' => [
                        'expiryDate' => 'foo',
                        'maskedPan' => 'bar',
                    ],
                ],
            ],
            200
        );

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveUnscheduledSubscription($unscheduledSubscriptionId);

        $this->assertSame($unscheduledSubscriptionId, $result->getUnscheduledSubscriptionId());
    }

    public function testItRetrievesUnscheduledSubscriptionWithUnknownCardDetailsKeys(): void
    {
        $unscheduledSubscriptionId = 'abc-123';
        $response = $this->createResponse(
            [
                'unscheduledSubscriptionId' => $unscheduledSubscriptionId,
                'paymentDetails' => [
                    'paymentType' => 'CARD',
                    'paymentMethod' => 'Visa',
                    'cardDetails' => [
                        'expiryDate' => 'foo',
                        'maskedPan' => 'bar',
                        'cardBrand' => 'Visa',
                    ],
                ],
            ],
            200
        );

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveUnscheduledSubscription($unscheduledSubscriptionId);

        $this->assertSame($unscheduledSubscriptionId, $result->getUnscheduledSubscriptionId());
    }

    public function testItThrowsExceptionOnClientErrorRetrieveUnscheduledSubscription(): void
    {
        $this->expectException(ClientErrorPaymentApiException::class);

        $response = $this->createResponse([
            'errors' => [
                'property1' => ['string'],
            ],
        ], 400);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));
        $sut->retrieveUnscheduledSubscription('abc-123');
    }

    public function testItThrowsExceptionOnServerErrorRetrieveUnscheduledSubscription(): void
    {
        $this->expectException(PaymentApiException::class);

        $response = $this->createResponse([], 500);

        $sut = $this->createSubscriptionApi($response, $this->createStub(StreamFactoryInterface::class));
        $sut->retrieveUnscheduledSubscription('abc-123');
    }

    public function testItBulkChargesSubscription(): void
    {
        $bulkId = '50490f2b-98bd-4782-b08d-413ee70aa1f7';

        $response = $this->createResponse([
            'bulkId' => $bulkId,
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->bulkChargeSubscription($this->createBulkChargeSubscriptionRequest());

        $this->assertSame($bulkId, $result->getBulkId());
    }

    public function testItRetrievesSubscriptionBulkCharges(): void
    {
        $subscriptionId = 'foo';
        $bulkId = '50490f2b-98bd-4782-b08d-413ee70aa1f7';

        $response = $this->createResponse([
            'page' => [
                [
                    'subscriptionId' => $subscriptionId,
                    'paymentId' => '1234',
                    'chargeId' => '123456789',
                    'status' => 'Succeeded',
                ],
            ],
            'more' => false,
            'status' => 'Done',
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveSubscriptionBulkCharges($bulkId);

        $this->assertSame($subscriptionId, $result->getPage()[0]->getSubscriptionId());
        $this->assertSame(ChargeStatusEnum::SUCCEEDED, $result->getPage()[0]->getChargeStatus());
        $this->assertFalse($result->isMore());
        $this->assertSame(BulkOperationStatusEnum::DONE, $result->getBulkOperationStatus());
    }

    public function testItRetrievesBulkVerifications(): void
    {
        $subscriptionId = 'foo';
        $bulkId = '50490f2b-98bd-4782-b08d-413ee70aa1f7';

        $response = $this->createResponse([
            'page' => [
                [
                    'subscriptionId' => $subscriptionId,
                    'paymentId' => '1234',
                    'chargeId' => '123456789',
                    'status' => 'Succeeded',
                ],
            ],
            'more' => false,
            'status' => 'Done',
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveBulkVerifications($bulkId);

        $this->assertSame($subscriptionId, $result->getPage()[0]->getSubscriptionId());
        $this->assertSame(VerificationStatusEnum::SUCCEEDED, $result->getPage()[0]->getVerificationStatusEnum());
        $this->assertFalse($result->isMore());
        $this->assertSame(BulkOperationStatusEnum::DONE, $result->getBulkOperationStatus());
    }

    public function testItRetrievesBulkVerificationsForUnscheduledSubscriptions(): void
    {
        $unscheduledSubscriptionId = '0279ca55dbc24ea697e186c3eec34b65';
        $bulkId = '50490f2b-98bd-4782-b08d-413ee70aa1f7';

        $response = $this->createResponse([
            'page' => [
                [
                    'unscheduledSubscriptionId' => $unscheduledSubscriptionId,
                    'paymentId' => '472e651e-5a1e-424d-8098-23858bf03ad7',
                    'status' => 'Succeeded',
                    'externalReference' => 'ref-123',
                ],
            ],
            'more' => false,
            'status' => 'Done',
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveBulkVerificationsForUnscheduledSubscriptions($bulkId);

        $this->assertSame($unscheduledSubscriptionId, $result->getPage()[0]->getUnscheduledSubscriptionId());
        $this->assertSame(VerificationStatusEnum::SUCCEEDED, $result->getPage()[0]->getVerificationStatus());
        $this->assertSame('ref-123', $result->getPage()[0]->getExternalReference());
        $this->assertFalse($result->isMore());
        $this->assertSame(BulkOperationStatusEnum::DONE, $result->getBulkOperationStatus());
    }

    public function testItRetrievesBulkVerificationsForUnscheduledSubscriptionsWithPagination(): void
    {
        $bulkId = '50490f2b-98bd-4782-b08d-413ee70aa1f7';

        $response = $this->createResponse([
            'page' => [],
            'more' => true,
            'status' => 'Processing',
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveBulkVerificationsForUnscheduledSubscriptions($bulkId, skip: 10, take: 5);

        $this->assertTrue($result->isMore());
        $this->assertSame(BulkOperationStatusEnum::PROCESSING, $result->getBulkOperationStatus());
    }

    public function testItThrowsExceptionOnClientErrorRetrieveBulkVerificationsForUnscheduledSubscriptions(): void
    {
        $this->expectException(ClientErrorPaymentApiException::class);

        $response = $this->createResponse([
            'errors' => [
                'property1' => ['string'],
            ],
        ], 400);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));
        $sut->retrieveBulkVerificationsForUnscheduledSubscriptions('bulk-id');
    }

    public function testItThrowsExceptionOnServerErrorRetrieveBulkVerificationsForUnscheduledSubscriptions(): void
    {
        $this->expectException(PaymentApiException::class);

        $response = $this->createResponse([], 500);

        $sut = $this->createSubscriptionApi($response, $this->createStub(StreamFactoryInterface::class));
        $sut->retrieveBulkVerificationsForUnscheduledSubscriptions('bulk-id');
    }

    public function testItVerifySubscriptions(): void
    {
        $bulkId = '50490f2b-98bd-4782-b08d-413ee70aa1f7';

        $response = $this->createResponse([
            'bulkId' => $bulkId,
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->verifySubscriptions(new VerifySubscriptions(
            [
                new Subscription('foo'),
                new Subscription(externalReference: 'bar'),
            ],
            $bulkId
        ));

        $this->assertSame($bulkId, $result->getBulkId());
    }

    public function testItChargesSubscription(): void
    {
        $paymentId = '472e651e-5a1e-424d-8098-23858bf03ad7';
        $chargeId = 'aec0aceb-a4db-49fb-b366-75e90229c640';

        $response = $this->createResponse([
            'paymentId' => $paymentId,
            'chargeId' => $chargeId,
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->chargeSubscription('subscriptionId', $this->createChargeSubscriptionRequest());

        $this->assertSame($paymentId, $result->getPaymentId());
        $this->assertSame($chargeId, $result->getChargeId());
    }

    public function testItChargesUnscheduledSubscription(): void
    {
        $paymentId = '472e651e-5a1e-424d-8098-23858bf03ad7';
        $chargeId = 'aec0aceb-a4db-49fb-b366-75e90229c640';

        $response = $this->createResponse([
            'paymentId' => $paymentId,
            'chargeId' => $chargeId,
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->chargeUnscheduledSubscription('subscriptionId', $this->createChargeUnscheduledSubscriptionRequest());

        $this->assertSame($paymentId, $result->getPaymentId());
        $this->assertSame($chargeId, $result->getChargeId());
    }

    public function testItThrowsExceptionWhenChargingUnscheduledSubscriptionRequestFails(): void
    {
        $subscriptionId = 'subscriptionId';
        $httpClientException = new HttpClientException('Request failed', 503);

        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects($this->once())
            ->method('post')
            ->willThrowException($httpClientException);

        $sut = new SubscriptionApi($httpClient);
        $this->expectException(PaymentApiException::class);
        $sut->chargeUnscheduledSubscription($subscriptionId, $this->createChargeUnscheduledSubscriptionRequest());
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

    private function createBulkChargeSubscriptionRequest(): BulkChargeSubscription
    {
        return new BulkChargeSubscription(
            'foo',
            new Notification([new Webhook('foo', 'bar', 'baz')]),
            []
        );
    }

    private function createChargeSubscriptionRequest(): ChargeSubscription
    {
        return new ChargeSubscription(
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
            new Notification([new Webhook('foo', 'bar', 'baz')]),
        );
    }

    private function createChargeUnscheduledSubscriptionRequest(): ChargeUnscheduledSubscription
    {
        return new ChargeUnscheduledSubscription(
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
            new Notification([new Webhook('foo', 'bar', 'baz')]),
        );
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

    private function createSubscriptionApi(ResponseInterface $response, StreamFactoryInterface $streamFactory): SubscriptionApi
    {
        return new SubscriptionApi(
            new HttpClient(
                $this->createPsrClient($response),
                $this->createRequestFactoryStub(),
                $streamFactory,
                new Configuration('1234', 'https://api.example.com')
            ),
        );
    }

    private function createStreamFactory(StreamInterface $stream): StreamFactoryInterface
    {
        $streamFactory = $this->createStub(StreamFactoryInterface::class);
        $streamFactory->method('createStream')->willReturn($stream);

        return $streamFactory;
    }
}
