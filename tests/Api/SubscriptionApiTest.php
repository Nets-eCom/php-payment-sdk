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
use NexiCheckout\Model\Request\BulkChargeUnscheduledSubscription;
use NexiCheckout\Model\Request\BulkChargeUnscheduledSubscription\UnscheduledSubscription as BulkUnscheduledSubscription;
use NexiCheckout\Model\Request\ChargeSubscription;
use NexiCheckout\Model\Request\ChargeUnscheduledSubscription;
use NexiCheckout\Model\Request\Item;
use NexiCheckout\Model\Request\Shared\Notification;
use NexiCheckout\Model\Request\Shared\Notification\Webhook;
use NexiCheckout\Model\Request\Shared\Order;
use NexiCheckout\Model\Request\VerifySubscriptions;
use NexiCheckout\Model\Request\VerifySubscriptions\Subscription;
use NexiCheckout\Model\Request\VerifyUnscheduledSubscriptions;
use NexiCheckout\Model\Request\VerifyUnscheduledSubscriptions\UnscheduledSubscription;
use NexiCheckout\Model\Result\Shared\BulkOperationStatusEnum;
use NexiCheckout\Model\Result\Shared\VerificationStatusEnum;
use NexiCheckout\Model\Result\SubscriptionCharges\ChargeStatusEnum;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public function testItRetrievesUnscheduledSubscriptionByExternalReference(): void
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

        $result = $sut->retrieveUnscheduledSubscriptionByExternalReference('ext-ref-123');

        $this->assertSame($unscheduledSubscriptionId, $result->getUnscheduledSubscriptionId());
    }

    public function testItThrowsExceptionOnClientErrorRetrieveUnscheduledSubscriptionByExternalReference(): void
    {
        $this->expectException(ClientErrorPaymentApiException::class);

        $response = $this->createResponse([
            'errors' => [
                'property1' => ['string'],
            ],
        ], 400);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));
        $sut->retrieveUnscheduledSubscriptionByExternalReference('ext-ref-123');
    }

    public function testItThrowsExceptionOnServerErrorRetrieveUnscheduledSubscriptionByExternalReference(): void
    {
        $this->expectException(PaymentApiException::class);

        $response = $this->createResponse([], 500);

        $sut = $this->createSubscriptionApi($response, $this->createStub(StreamFactoryInterface::class));
        $sut->retrieveUnscheduledSubscriptionByExternalReference('ext-ref-123');
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

    public function testItBulkChargesUnscheduledSubscriptions(): void
    {
        $bulkId = '878a443c-e535-48b3-aa7a-2853592c3ce8';

        $response = $this->createResponse([
            'bulkId' => $bulkId,
        ], 202);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->bulkChargeUnscheduledSubscriptions($this->createBulkChargeUnscheduledSubscriptionRequest());

        $this->assertSame($bulkId, $result->getBulkId());
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

    public function testItRetrievesBulkUnscheduledCharges(): void
    {
        $unscheduledSubscriptionId = '6a3e3ab66c374d50b779a3d0b8447f92';
        $bulkId = '384760c02faf45a69376a3edff6c8415';

        $response = $this->createResponse([
            'page' => [
                [
                    'unscheduledSubscriptionId' => $unscheduledSubscriptionId,
                    'paymentId' => '472e651e-5a1e-424d-8098-23858bf03ad7',
                    'chargeId' => 'aec0aceb-a4db-49fb-b366-75e90229c640',
                    'status' => 'Succeeded',
                    'externalReference' => 'ref-123',
                ],
            ],
            'more' => false,
            'status' => 'Done',
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveBulkUnscheduledCharges($bulkId);

        $this->assertSame($unscheduledSubscriptionId, $result->getPage()[0]->getUnscheduledSubscriptionId());
        $this->assertSame(ChargeStatusEnum::SUCCEEDED, $result->getPage()[0]->getStatus());
        $this->assertSame('ref-123', $result->getPage()[0]->getExternalReference());
        $this->assertFalse($result->isMore());
        $this->assertSame(BulkOperationStatusEnum::DONE, $result->getBulkOperationStatus());
    }

    public function testItRetrievesBulkUnscheduledChargesWithPagination(): void
    {
        $bulkId = '384760c02faf45a69376a3edff6c8415';

        $response = $this->createResponse([
            'page' => [],
            'more' => true,
            'status' => 'Processing',
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveBulkUnscheduledCharges($bulkId, skip: 10, take: 5);

        $this->assertTrue($result->isMore());
        $this->assertSame(BulkOperationStatusEnum::PROCESSING, $result->getBulkOperationStatus());
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

    public function testItVerifyUnscheduledSubscriptions(): void
    {
        $bulkId = '50490f2b-98bd-4782-b08d-413ee70aa1f7';

        $response = $this->createResponse([
            'bulkId' => $bulkId,
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->verifyUnscheduledSubscriptions(new VerifyUnscheduledSubscriptions(
            [
                new UnscheduledSubscription('abc-123'),
                new UnscheduledSubscription(externalReference: 'ext-ref'),
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

    /**
     * @param mixed[] $arguments
     * @param mixed[] $resposeBody
     */
    #[DataProvider('methodsClientError')]
    public function testItThrowsExceptionOnClientError(
        string $expectedException,
        string $methodName,
        array $arguments,
        int $errorCode,
        ?array $resposeBody
    ): void {
        $this->expectException($expectedException);

        $response = $this->createResponse($resposeBody, $errorCode);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));
        $sut->{$methodName}(...$arguments);
    }

    /**
     * @return iterable<array{string, string, mixed[], int, ?mixed[]}>
     */
    public static function methodsClientError(): iterable
    {
        yield [
            ClientErrorPaymentApiException::class, 'retrieveUnscheduledSubscription', ['abc-123'], 400, [
                'errors' => [
                    'property1' => ['string'],
                ],
            ]];
        yield [
            PaymentApiException::class, 'retrieveUnscheduledSubscription', ['abc-123'], 500, [
                'message' => 'Internal Server Error',
                'code' => 1000,
            ]];
        yield [
            ClientErrorPaymentApiException::class,
            'retrieveBulkVerificationsForUnscheduledSubscriptions',
            ['bulk-id'],
            400,
            [
                'errors' => [
                    'property1' => ['string'],
                ],
            ],
        ];
        yield [
            ClientErrorPaymentApiException::class,
            'verifyUnscheduledSubscriptions',
            [new VerifyUnscheduledSubscriptions([], 'bulk-id')],
            400,
            [
                'errors' => [
                    'property1' => ['string'],
                ],
            ],
        ];
        yield [
            ClientErrorPaymentApiException::class,
            'retrieveBulkUnscheduledCharges',
            ['384760c02faf45a69376a3edff6c8415'],
            400,
            [
                'errors' => [
                    'property1' => ['string'],
                ],
            ],
        ];
        yield [
            PaymentApiException::class,
            'retrieveBulkUnscheduledCharges',
            ['384760c02faf45a69376a3edff6c8415'],
            500,
            [
                'message' => 'Internal Server Error',
                'code' => 1000,
            ],
        ];
        yield [
            ClientErrorPaymentApiException::class,
            'bulkChargeUnscheduledSubscriptions',
            [new BulkChargeUnscheduledSubscription([])],
            400,
            [
                'errors' => [
                    'property1' => ['string'],
                ],
            ],
        ];
    }

    /**
     * @param mixed[] $arguments
     */
    #[DataProvider('methodsServerError')]
    public function testMethodsThrowsExceptionOnServerError(string $methodName, array $arguments, string $httpMethod, int $errorCode): void
    {
        $httpClient = $this->createMock(HttpClient::class);
        $httpClient->expects($this->once())
            ->method($httpMethod)
            ->willThrowException(new HttpClientException('Request failed', $errorCode));

        $sut = new SubscriptionApi($httpClient);
        $this->expectException(PaymentApiException::class);
        $sut->{$methodName}(...$arguments);
    }

    /**
     * @return iterable<array{string, mixed[], string, int}>
     */
    public static function methodsServerError(): iterable
    {
        yield ['retrieveUnscheduledSubscription', ['abc-123'], 'get', 504];
        yield ['retrieveBulkVerificationsForUnscheduledSubscriptions', ['bulk-id'], 'get', 504];
        yield ['chargeUnscheduledSubscription', ['subscriptionId', new ChargeUnscheduledSubscription(new Order([], 'SEK', 1), null)], 'post', 503];
        yield ['verifyUnscheduledSubscriptions', [new VerifyUnscheduledSubscriptions([], 'bulk-id')], 'post', 504];
        yield ['retrieveBulkUnscheduledCharges', ['384760c02faf45a69376a3edff6c8415'], 'get', 504];
    }

    public function testItRetrievesStatusOfUnscheduledSubscription(): void
    {
        $paymentId = '472e651e-5a1e-424d-8098-23858bf03ad7';
        $chargeId = 'aec0aceb-a4db-49fb-b366-75e90229c640';
        $subscriptionId = 'subscriptionId';

        $response = $this->createResponse([
            'paymentId' => $paymentId,
            'chargeId' => $chargeId,
            'completed' => true,
        ], 200);

        $sut = $this->createSubscriptionApi($response, $this->createStreamFactory($response->getBody()));

        $result = $sut->retrieveUnscheduledSubscriptionChargeStatus($subscriptionId);

        $this->assertSame($paymentId, $result->getPaymentId());
        $this->assertSame($chargeId, $result->getChargeId());
        $this->assertTrue($result->getCompleted());
    }

    public function testItThrowsExceptionWhenCheckingUnscheduledSubscriptionStatusFails(): void
    {
        $subscriptionId = 'subscriptionId';
        $idempotencyKey = 'idempotencyKey';

        $this->expectException(PaymentApiException::class);

        $response = $this->createResponse([], 404);

        $sut = $this->createSubscriptionApi($response, $this->createStub(StreamFactoryInterface::class));
        $sut->retrieveUnscheduledSubscriptionChargeStatus($subscriptionId, $idempotencyKey);
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

    private function createBulkChargeUnscheduledSubscriptionRequest(): BulkChargeUnscheduledSubscription
    {
        return new BulkChargeUnscheduledSubscription(
            [
                new BulkUnscheduledSubscription(
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
                    unscheduledSubscriptionId: '92143051-9e78-40af-a01f-245ccdcd9c03',
                    myReference: 'my-reference'
                ),
            ],
            'foo',
            new Notification([new Webhook('foo', 'bar', 'baz')]),
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
