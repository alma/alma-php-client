<?php

namespace Alma\API\Tests\Unit\Endpoints;

use Alma\API\Endpoints\MerchantService;
use Alma\API\Entities\DTO\MerchantBusinessEvent\CartInitiatedBusinessEvent;
use Alma\API\Entities\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEvent;
use Alma\API\Exceptions\MerchantServiceException;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;
use Psr\Log\LoggerInterface;

class MerchantServiceTest extends AbstractEndpointServiceTest
{
    /** @var MerchantService|Mock */
    private $merchantService;

    public function setUp(): void
    {
        parent::setUp();

        // Mocks
        $this->merchantService = Mockery::mock(MerchantService::class, [$this->clientMock])->makePartial();
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');
        $this->responseMock = Mockery::mock(Response::class);
    }

    /**
     * @throws MerchantServiceException
     */
    public function testSendBusinessEventBadResponseMerchantServiceException()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(true);
        $responseMock->shouldReceive('getReasonPhrase')->andReturn('Request error');
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Expectations
        $this->expectException(MerchantServiceException::class);

        // Call
        $this->merchantService->sendCartInitiatedBusinessEvent(new CartInitiatedBusinessEvent('42'));
    }

    /**
     * @throws MerchantServiceException
     */
    public function testSendCartInitiatedEvent()
    {
        // Params
        $cartInitiatedEvent = new CartInitiatedBusinessEvent('42');

        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->shouldReceive('getReasonPhrase')->andReturn('Request error');
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Call
        $this->merchantService->sendCartInitiatedBusinessEvent($cartInitiatedEvent);
    }

    /**
     * @throws MerchantServiceException
     */
    public function testSendOrderConfirmedBusinessEventForNonAlmaPayment()
    {
        // Params
        $orderConfirmedBusinessEvent = new OrderConfirmedBusinessEvent(
            false,
            false,
            true,
            '42',
            '54'
        );

        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->shouldReceive('getReasonPhrase')->andReturn('oops');
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Call
        $this->merchantService->sendOrderConfirmedBusinessEvent($orderConfirmedBusinessEvent);
    }

    /**
     * @throws MerchantServiceException
     */
    public function testSendOrderConfirmedBusinessEventForAlmaPayment()
    {
        // Params
        $orderConfirmedBusinessEvent = new OrderConfirmedBusinessEvent(
            true,
            false,
            true,
            '42',
            '54',
            'alma_payment_id'
        );

        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->shouldReceive('getReasonPhrase')->andReturn('oops');
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Call
        $this->merchantService->sendOrderConfirmedBusinessEvent($orderConfirmedBusinessEvent);
    }
}
