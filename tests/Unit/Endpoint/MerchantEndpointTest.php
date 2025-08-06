<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\DTO\MerchantBusinessEvent\CartInitiatedBusinessEventDtoDto;
use Alma\API\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEventDtoDto;
use Alma\API\Endpoint\MerchantEndpoint;
use Alma\API\Entity\FeePlan;
use Alma\API\Entity\Merchant;
use Alma\API\Exception\ClientException;
use Alma\API\Exception\Endpoint\MerchantEndpointException;
use Alma\API\Exception\RequestException;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;
use Psr\Log\LoggerInterface;

class MerchantEndpointTest extends AbstractEndpointSetUp
{
    const DEFAULT_JSON_RESPONSE = '{"json_key": "json_value"}';

    const FEE_PLAN_JSON_RESPONSE = '[
        {
            "installmentsCount": 3,
            "kind": "general",
            "deferredMonths": 0,
            "deferredDays": 0,
            "deferredTriggerLimitDays": 0,
            "allowed": 1,
            "min_purchase_amount": 500,
            "max_purchase_amount": 1000
        }
    ]';

    /** @var MerchantEndpoint|Mock */
    private $merchantService;

    public function setUp(): void
    {
        parent::setUp();

        // Mocks
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');

        $this->responseMock = Mockery::mock(Response::class);
        $this->responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->responseMock->shouldReceive('isError')->andReturn(false);
        $this->responseMock->shouldReceive('getBody')->andReturn(self::FEE_PLAN_JSON_RESPONSE);
        $this->responseMock->shouldReceive('getJson')->andReturn(json_decode(self::FEE_PLAN_JSON_RESPONSE, true));

        $this->badResponseMock = Mockery::mock(Response::class);
        $this->badResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $this->badResponseMock->shouldReceive('isError')->andReturn(true);
        $this->badResponseMock->shouldReceive('getBody')->andReturn(self::FEE_PLAN_JSON_RESPONSE);

        $this->merchantService = Mockery::mock(MerchantEndpoint::class, [$this->clientMock])->makePartial();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * Ensure we can create a DataExport
     * @throws MerchantEndpointException
     */
    public function testMe()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // MerchantService
        $result = $this->merchantService->me();

        // Assertions
        $this->assertInstanceOf(Merchant::class, $result);
    }

    /**
     * Ensure we can catch DataExportServiceException
     * @throws MerchantEndpointException
     */
    public function testMeMerchantServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(MerchantEndpointException::class);

        // Call
        $this->merchantService->me();
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws MerchantEndpointException
     */
    public function testMeRequestException()
    {
        // Mocks
        $merchantServiceMock = Mockery::mock(MerchantEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $merchantServiceMock->shouldReceive('createGetRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(MerchantEndpointException::class);
        $merchantServiceMock->me();
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws MerchantEndpointException
     */
    public function testMeClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(MerchantEndpointException::class);
        $this->merchantService->me();
    }

    /**
     * Ensure we get fee plans
     * @throws MerchantEndpointException
     */
    public function testFeePlans()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->responseMock);

        // MerchantService
        $result = $this->merchantService->getFeePlanList();
        $resultWithArray = $this->merchantService->getFeePlanList(FeePlan::KIND_GENERAL, [3]);

        // Assertions
        foreach ($result as $feePlan) {
            $this->assertInstanceOf(FeePlan::class, $feePlan);
        }
        foreach ($resultWithArray as $feePlan) {
            $this->assertInstanceOf(FeePlan::class, $feePlan);
        }
    }

    /**
     * Ensure we can catch API errors
     * @throws MerchantEndpointException
     */
    public function testFeePlansMerchantServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(MerchantEndpointException::class);

        // Call
        $this->merchantService->getFeePlanList();
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws MerchantEndpointException
     */
    public function testFeePlansRequestException()
    {
        // Mocks
        $merchantServiceMock = Mockery::mock(MerchantEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $merchantServiceMock->shouldReceive('createGetRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(MerchantEndpointException::class);
        $merchantServiceMock->getFeePlanList();
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws MerchantEndpointException
     */
    public function testFeePlansClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(MerchantEndpointException::class);
        $this->merchantService->getFeePlanList();
    }

    /**
     * Ensure we can send a business event for a cart initiated
     * @throws MerchantEndpointException
     */
    public function testSendCartInitiatedBusinessEvent()
    {
        // Params
        $cartInitiatedEvent = new CartInitiatedBusinessEventDtoDto('42');

        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // Call
        $this->merchantService->sendCartInitiatedBusinessEvent($cartInitiatedEvent);
    }


    /**
     * Ensure we can send a business event for an order confirmed
     * @throws MerchantEndpointException
     */
    public function testSendOrderConfirmedBusinessEventForNonAlmaPayment()
    {
        // Params
        $orderConfirmedBusinessEvent = new OrderConfirmedBusinessEventDtoDto(
            false,
            false,
            true,
            '42',
            '54'
        );

        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // Call
        $this->merchantService->sendOrderConfirmedBusinessEvent($orderConfirmedBusinessEvent);
    }

    /**
     * Ensure we can send a business event for an order confirmed
     * @throws MerchantEndpointException
     */
    public function testSendOrderConfirmedBusinessEventForAlmaPayment()
    {
        // Params
        $orderConfirmedBusinessEvent = new OrderConfirmedBusinessEventDtoDto(
            true,
            false,
            true,
            '42',
            '54',
            'alma_payment_id'
        );

        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // Call
        $this->merchantService->sendOrderConfirmedBusinessEvent($orderConfirmedBusinessEvent);
    }

    /**
     * Ensure we can catch API errors
     * @throws MerchantEndpointException
     */
    public function testSendBusinessEventMerchantServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(MerchantEndpointException::class);

        // Call
        $this->merchantService->sendCartInitiatedBusinessEvent(new CartInitiatedBusinessEventDtoDto('42'));
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws MerchantEndpointException
     */
    public function testSendBusinessEventRequestException()
    {
        // Mocks
        $merchantServiceMock = Mockery::mock(MerchantEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $merchantServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(MerchantEndpointException::class);
        $merchantServiceMock->sendCartInitiatedBusinessEvent(new CartInitiatedBusinessEventDtoDto('42'));
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws MerchantEndpointException
     */
    public function testSendBusinessEventClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(MerchantEndpointException::class);
        $this->merchantService->sendCartInitiatedBusinessEvent(new CartInitiatedBusinessEventDtoDto('42'));
    }
}
