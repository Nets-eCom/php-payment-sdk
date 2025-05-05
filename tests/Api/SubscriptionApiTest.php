<?php

declare(strict_types=1);

namespace NexiCheckout\Tests\Api;

use NexiCheckout\Api\SubscriptionApi;
use NexiCheckout\Http\Configuration;
use NexiCheckout\Http\HttpClient;
use NexiCheckout\Model\Request\BulkChargeSubscription;
use NexiCheckout\Model\Request\Shared\Notification;
use NexiCheckout\Model\Request\Shared\Notification\Webhook;
use NexiCheckout\Model\Request\VerifySubscriptions;
use NexiCheckout\Model\Request\VerifySubscriptions\Subscription;
use NexiCheckout\Model\Result\RetrieveBulkVerifications\VerificationStatusEnum;
use NexiCheckout\Model\Result\Shared\BulkOperationStatusEnum;
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
