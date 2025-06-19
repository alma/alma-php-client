<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Endpoint\OrderEndpoint;
use Alma\API\Entities\Order;
use Alma\API\Exceptions\ClientException;
use Alma\API\Exceptions\Endpoint\OrderEndpointException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Lib\ArrayUtils;
use Alma\API\PaginatedResult;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;
use Psr\Log\LoggerInterface;

class OrderEndpointTest extends AbstractEndpointSetUp
{
    const DEFAULT_JSON_RESPONSE = '{"json_key": "json_value"}';

    const FETCH_ALL_ORDERS_RESPONSE_JSON = '{
        "data": [
            {
                "comment": "comment",
                "created": 1747829359,
                "customer_url": "customer_url",
                "data": {},
                "id": "order_1213IpL5UdjoOqjgdljmNPMB3MOJw5vtgd",
                "merchant_reference": "C1-000027951",
                "merchant_url": "merchant_url",
                "payment": "payment_1213Ioh6xMAUYk7OTZ52VHNgGGwgT9B4ro",
                "updated": 1747829359
            },
            {
                "comment": "comment",
                "created": 1747829359,
                "customer_url": "customer_url",
                "data": {},
                "id": "order_1213IpL5UdjoOqjgdljmNPMB3MOJw6vtgd",
                "merchant_reference": "C1-000027951",
                "merchant_url": "merchant_url",
                "payment": "payment_1213Ioh6xMAUYk7OTZ52VHNgGGwgT9B4ro",
                "updated": 1747829359
            }
        ],
        "has_more": true
    }';

    /** @var string JSON response for add order*/
    const SERVER_REQUEST_ORDER_RESPONSE_JSON = '[
        {
            "comment": "comment",
            "created": 1747829359,
            "customer_url": "customer_url",
            "data": {},
            "id": "order_1213IpL5UdjoOqjgdljmNPMB3MOJw5vtgd",
            "merchant_reference": "C1-000027951",
            "merchant_url": "merchant_url",
            "payment": "payment_1213Ioh6xMAUYk7OTZ52VHNgGGwgT9B4ro",
            "updated": 1747829359
        }
    ]';

    CONST FETCH_ORDER_RESPONSE_JSON = '{
            "comment": "comment",
            "created": 1747829359,
            "customer_url": "customer_url",
            "data": {},
            "id": "order_1213IpL5UdjoOqjgdljmNPMB3MOJw5vtgd",
            "merchant_reference": "C1-000027951",
            "merchant_url": "merchant_url",
            "payment": "payment_1213Ioh6xMAUYk7OTZ52VHNgGGwgT9B4ro",
            "updated": 1747829359
        }';

    /** @var OrderEndpoint|Mock */
    protected $orderServiceMock;

    /** @var ArrayUtils|Mock */
    protected $arrayUtilsMock;

    /** @var Response|Mock */
    protected $responseMock;

    public function setUp(): void
    {
        parent::setUp();

        // Mocks
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');

        $this->responseMock = Mockery::mock(Response::class);
        $this->responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->responseMock->shouldReceive('isError')->andReturn(false);
        $this->responseMock->shouldReceive('getBody')->andReturn(self::SERVER_REQUEST_ORDER_RESPONSE_JSON);
        $this->responseMock->shouldReceive('getJson')->andReturn(json_decode(self::SERVER_REQUEST_ORDER_RESPONSE_JSON, true));

        $this->badResponseMock = Mockery::mock(Response::class);
        $this->badResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $this->badResponseMock->shouldReceive('isError')->andReturn(true);
        $this->badResponseMock->shouldReceive('getBody')->andReturn(self::SERVER_REQUEST_ORDER_RESPONSE_JSON);

        $this->fetchAllResponseMock = Mockery::mock(Response::class);
        $this->fetchAllResponseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->fetchAllResponseMock->shouldReceive('isError')->andReturn(false);
        $this->fetchAllResponseMock->shouldReceive('getBody')->andReturn(self::FETCH_ALL_ORDERS_RESPONSE_JSON);
        $this->fetchAllResponseMock->shouldReceive('getJson')->andReturn(json_decode(self::FETCH_ALL_ORDERS_RESPONSE_JSON, true));

        $this->badFetchAllResponseMock = Mockery::mock(Response::class);
        $this->badFetchAllResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badFetchAllResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $this->badFetchAllResponseMock->shouldReceive('isError')->andReturn(true);
        $this->badFetchAllResponseMock->shouldReceive('getBody')->andReturn(self::FETCH_ALL_ORDERS_RESPONSE_JSON);

        $this->fetchResponseMock = Mockery::mock(Response::class);
        $this->fetchResponseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->fetchResponseMock->shouldReceive('isError')->andReturn(false);
        $this->fetchResponseMock->shouldReceive('getBody')->andReturn(self::FETCH_ORDER_RESPONSE_JSON);
        $this->fetchResponseMock->shouldReceive('getJson')->andReturn(json_decode(self::FETCH_ORDER_RESPONSE_JSON, true));

        $this->badFetchResponseMock = Mockery::mock(Response::class);
        $this->badFetchResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badFetchResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $this->badFetchResponseMock->shouldReceive('isError')->andReturn(true);
        $this->badFetchResponseMock->shouldReceive('getBody')->andReturn(self::FETCH_ORDER_RESPONSE_JSON);

        // OrderService
        $this->orderServiceMock = Mockery::mock(OrderEndpoint::class, [$this->clientMock])->makePartial();
    }

    /**
     * Ensure we can create a DataExport
     * @throws OrderEndpointException
     */
    public function testUpdateOrder()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // OrderService
        $result = $this->orderServiceMock->update('order_id');

        // Assertions
        $this->assertInstanceOf(Order::class, $result);
    }

    /**
     * Ensure we can catch DataExportServiceException
     * @throws OrderEndpointException
     */
    public function testUpdateOrderOrderServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);// self::DEFAULT_JSON_RESPONSE

        // Expectations
        $this->expectException(OrderEndpointException::class);

        // Call
        $this->orderServiceMock->update('order_id');
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws OrderEndpointException
     */
    public function testUpdateOrderRequestException()
    {
        // Mocks
        $orderServiceMock = Mockery::mock(OrderEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $orderServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(OrderEndpointException::class);
        $orderServiceMock->update('order_id');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws OrderEndpointException
     */
    public function testUpdateOrderClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(OrderEndpointException::class);
        $this->orderServiceMock->update('order_id');
    }

    /**
     * @return void
     * @throws OrderEndpointException
     */
    public function testAddTracking()
    {
        // Params
        $trackingData = [
            'carrier' => 'UPS',
            'tracking_number' => 'UPS_123456',
            'tracking_url' => 'https://tracking.com'
        ];

        // Mocks
        $this->clientMock->shouldReceive('sendRequest')
            ->once()
            ->andReturn($this->responseMock);

        // Call
        $this->orderServiceMock->addTracking(
            '123',
            $trackingData['carrier'],
            $trackingData['tracking_number'],
            $trackingData['tracking_url']
        );
    }

    /**
     * @throws OrderEndpointException
     */
    public function testAddTrackingOrderServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')
            ->once()
            ->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(OrderEndpointException::class);

        // Call
        $this->orderServiceMock->addTracking('123', 'ups', '123456', 'myUrl');
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws OrderEndpointException
     */
    public function testAddTrackingRequestException()
    {
        // Mocks
        $orderServiceMock = Mockery::mock(OrderEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $orderServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(OrderEndpointException::class);
        $orderServiceMock->addTracking('order_id', 'carrier', 'tracking_number', 'tracking_url');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws OrderEndpointException
     */
    public function testAddTrackingClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(OrderEndpointException::class);
        $this->orderServiceMock->addTracking('order_id', 'carrier', 'tracking_number', 'tracking_url');
    }

    public function testValidateStatusDataNoOrderData()
    {
        // Expectations
        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Missing in the required parameters (status, is_shipped) when calling orders.sendStatus');

        // Call
        $this->orderServiceMock->validateStatusData();
    }

    /**
     * Ensure we can create a DataExport
     * @throws OrderEndpointException
     */
    public function testFetchAllOrders()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->times(5)->andReturn($this->fetchAllResponseMock);

        // Assertions
        $this->assertInstanceOf(PaginatedResult::class, $this->orderServiceMock->fetchAll());
        $this->assertInstanceOf(PaginatedResult::class, $this->orderServiceMock->fetchAll(20, 'starting_after'));
        $this->assertInstanceOf(PaginatedResult::class, $this->orderServiceMock->fetchAll(20, null, ['status' => 'pending']));
        $this->assertInstanceOf(PaginatedResult::class, $this->orderServiceMock->fetchAll(1)->nextPage());
    }

    /**
     * Ensure we can catch DataExportServiceException
     * @throws OrderEndpointException
     */
    public function testFetchAllOrdersOrderServiceException()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class)
            ->makePartial();
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badFetchAllResponseMock);

        // Expectations
        $this->expectException(OrderEndpointException::class);

        // Call
        $this->orderServiceMock->fetchAll();
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws OrderEndpointException
     */
    public function testFetchAllOrdersRequestException()
    {
        // Mocks
        $orderServiceMock = Mockery::mock(OrderEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $orderServiceMock->shouldReceive('createGetRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(OrderEndpointException::class);
        $orderServiceMock->fetchAll();
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws OrderEndpointException
     */
    public function testFetchAllOrdersClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(OrderEndpointException::class);
        $this->orderServiceMock->fetchAll();
    }

    /**
     * Ensure we can create a DataExport
     * @throws OrderEndpointException
     */
    public function testFetchOrder()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->fetchResponseMock);

        // OrderService
        $result = $this->orderServiceMock->fetch('order_id');

        // Assertions
        $this->assertInstanceOf(Order::class, $result);
    }

    /**
     * Ensure we can catch DataExportServiceException
     * @throws OrderEndpointException
     */
    public function testFetchOrderOrderServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badFetchResponseMock);

        // Expectations
        $this->expectException(OrderEndpointException::class);

        // Call
        $this->orderServiceMock->fetch('order_id');
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws OrderEndpointException
     */
    public function testFetchOrderRequestException()
    {
        // Mocks
        $orderServiceMock = Mockery::mock(OrderEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $orderServiceMock->shouldReceive('createGetRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(OrderEndpointException::class);
        $orderServiceMock->fetch('order_id');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws OrderEndpointException
     */
    public function testFetchOrderClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(OrderEndpointException::class);
        $this->orderServiceMock->fetch('order_id');
    }



    public function testValidateStatusDataMissingKeyException()
    {
        // OrderService
        $orderService = Mockery::mock(OrderEndpoint::class)->makePartial();

        // Expectations
        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Error in the required parameters (status, is_shipped) when calling orders.sendStatus');

        // Call
        $orderService->validateStatusData(array('status'));
    }

    public function testValidateStatusDataIsShippedNotBool()
    {
        // OrderService
        $orderService = Mockery::mock(OrderEndpoint::class)->makePartial();

        // Expectations
        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Parameter "is_shipped" must be a boolean');

        // Call
        $orderService->validateStatusData(array(
            'status' => 'Mon Status - 1',
            'is_shipped' => 'oui',
        ));
    }

    public function testValidateStatusDataStatusIsEmpty()
    {
        // OrderService
        $orderService = Mockery::mock(OrderEndpoint::class)->makePartial();

        // Expectations
        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Missing the required parameter "status" when calling orders.sendStatus');

        // Call
        $orderService->validateStatusData(array(
            'status' => '',
            'is_shipped' => true,
        ));
    }

}
