<?php

namespace Alma\API\Tests\Unit\Infrastructure\Endpoint;

use Alma\API\Application\DTO\MerchantBusinessEvent\CartInitiatedBusinessEventDto;
use Alma\API\Application\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEventDto;
use Alma\API\Domain\Entity\FeePlan;
use Alma\API\Domain\Entity\Merchant;
use Alma\API\Infrastructure\Endpoint\MerchantEndpoint;
use Alma\API\Infrastructure\Exception\ClientException;
use Alma\API\Infrastructure\Exception\Endpoint\MerchantEndpointException;
use Alma\API\Infrastructure\Exception\ParametersException;
use Alma\API\Infrastructure\Response;
use Mockery;
use Mockery\Mock;
use Psr\Log\LoggerInterface;

class MerchantEndpointTest extends AbstractEndpointSetUp
{
    const DEFAULT_JSON_RESPONSE = '{"json_key": "json_value"}';

    const MERCHANT_JSON_RESPONSE = '{
        "id": "string",
        "name": "string",
        "can_create_payments": true
    }';

    const FEE_PLAN_JSON_RESPONSE = '[
        {
            "allowed": 1,
            "available_online": 1,
            "customer_fee_variable": 380,
            "deferred_days": 1,
            "deferred_months": 2,
            "installments_count": 3,
            "kind": "general",
            "max_purchase_amount": 1000,
            "merchant_fee_variable": 12,
            "merchant_fee_fixed": 18,
            "min_purchase_amount": 500
        }
    ]';

    const BUSINEES_EVENT_JSON_RESPONSE = '{}';

    /** @var MerchantEndpoint|Mock */
    private $merchantService;
    private $feePlanResponseMock;
    private $meResponseMock;
    private $businessEventResponseMock;

    public function setUp(): void
    {
        parent::setUp();

        // Mocks
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');

        $this->feePlanResponseMock = Mockery::mock(Response::class);
        $this->feePlanResponseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->feePlanResponseMock->shouldReceive('isError')->andReturn(false);
        $this->feePlanResponseMock->shouldReceive('getBody')->andReturn(self::FEE_PLAN_JSON_RESPONSE);
        $this->feePlanResponseMock->shouldReceive('getJson')->andReturn(json_decode(self::FEE_PLAN_JSON_RESPONSE, true));

        $this->meResponseMock = Mockery::mock(Response::class);
        $this->meResponseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->meResponseMock->shouldReceive('isError')->andReturn(false);
        $this->meResponseMock->shouldReceive('getBody')->andReturn(self::MERCHANT_JSON_RESPONSE);
        $this->meResponseMock->shouldReceive('getJson')->andReturn(json_decode(self::MERCHANT_JSON_RESPONSE, true));

        $this->businessEventResponseMock = Mockery::mock(Response::class);
        $this->businessEventResponseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->businessEventResponseMock->shouldReceive('isError')->andReturn(false);
        $this->businessEventResponseMock->shouldReceive('getBody')->andReturn(self::BUSINEES_EVENT_JSON_RESPONSE);
        $this->businessEventResponseMock->shouldReceive('getJson')->andReturn(json_decode(self::BUSINEES_EVENT_JSON_RESPONSE, true));

        $this->badResponseMock = Mockery::mock(Response::class);
        $this->badResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $this->badResponseMock->shouldReceive('isError')->andReturn(true);
        $this->badResponseMock->shouldReceive('getBody')->andReturn(self::MERCHANT_JSON_RESPONSE);

        $this->merchantService = Mockery::mock(MerchantEndpoint::class, [$this->clientMock])->makePartial();
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * Ensure we can create a DataExport
     * @throws MerchantEndpointException|ParametersException
     */
    public function testMe()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->meResponseMock);

        // MerchantService
        $result = $this->merchantService->me();

        // Assertions
        $this->assertInstanceOf(Merchant::class, $result);
    }

    /**
     * Ensure we can catch DataExportServiceException
     * @throws MerchantEndpointException|ParametersException
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
     * Ensure we can catch ClientException
     * @return void
     * @throws MerchantEndpointException|ParametersException
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
     * @throws ParametersException
     */
    public function testFeePlans()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->feePlanResponseMock);

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
     * @throws MerchantEndpointException|ParametersException
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
     * Ensure we can catch ClientException
     * @return void
     * @throws MerchantEndpointException|ParametersException
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
        $cartInitiatedEvent = new CartInitiatedBusinessEventDto('42');

        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->businessEventResponseMock);

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
        $orderConfirmedBusinessEvent = new OrderConfirmedBusinessEventDto(
            false,
            false,
            true,
            '42',
            '54'
        );

        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->businessEventResponseMock);

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
        $orderConfirmedBusinessEvent = new OrderConfirmedBusinessEventDto(
            true,
            false,
            true,
            '42',
            '54',
            'alma_payment_id'
        );

        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->businessEventResponseMock);

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
        $this->merchantService->sendCartInitiatedBusinessEvent(new CartInitiatedBusinessEventDto('42'));
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
        $this->merchantService->sendCartInitiatedBusinessEvent(new CartInitiatedBusinessEventDto('42'));
    }
}
