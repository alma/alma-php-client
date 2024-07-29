<?php

namespace Alma\API\Tests\Unit\Endpoints;


use Alma\API\ClientContext;
use Alma\API\Endpoints\Orders;

use Alma\API\Entities\Order;
use Alma\API\Exceptions\AlmaException;
use Alma\API\Exceptions\MissingKeyException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Lib\ArrayUtils;
use Alma\API\Request;
use Alma\API\RequestError;
use Alma\API\Response;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Unit\Entities\OrderTest;

class OrdersTest extends TestCase
{

    /**
     * @var ClientContext
     */
    private $clientContext;
    /**
     * @var Orders
     */
    protected $orderEndpoint;
    /**
     * @var ArrayUtils
     */
    protected $arrayUtils;
    /**
     * @var Response
     */
    protected $responseMock;
    /**
     * @var Request
     */
    protected $requestObject;

    private $status;
    private $externalId;

    public function setUp(): void
    {
        $this->clientContext = Mockery::mock(ClientContext::class);
        $this->orderEndpoint = Mockery::mock(Orders::class)->makePartial();
        $this->arrayUtils = Mockery::mock(ArrayUtils::class)->makePartial();
        $this->responseMock = Mockery::mock(Response::class);
        $this->responseMock->errorMessage = 'Exception Error message';
        $this->requestObject = Mockery::mock(Request::class);
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');

        $this->orderEndpoint->arrayUtils = $this->arrayUtils;
        $this->externalId = 'Mon external Id';
        $this->status = 'Mon Status - 1';
        $this->clientContext->logger = $loggerMock;
        $this->orderEndpoint->setClientContext($this->clientContext);

    }

    public function tearDown(): void
    {
        $this->orderEndpoint = null;
        $this->arrayUtils = null;
        $this->responseMock = null;
        $this->requestObject = null;
        Mockery::close();
    }

    public function testValidateStatusDataNoOrderData()
    {
        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Missing in the required parameters (status, is_shipped) when calling orders.sendStatus');
        $this->orderEndpoint->validateStatusData();
    }

    public function testValidateStatusDataMissingSomeOrderData()
    {
        $orderEndpoint = Mockery::mock(Orders::class)->makePartial();
        $arrayUtils = Mockery::mock(ArrayUtils::class)->makePartial();
        $arrayUtils->shouldReceive('checkMandatoryKeys')->andThrow(new MissingKeyException());
        $orderEndpoint->arrayUtils = $arrayUtils;

        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Error in the required parameters (status, is_shipped) when calling orders.sendStatus');
        $orderEndpoint->validateStatusData(array('status'));
    }

    public function testValidateStatusDataIsShippedNotBool()
    {
        $orderEndpoint = Mockery::mock(Orders::class)->makePartial();
        $arrayUtils = Mockery::mock(ArrayUtils::class)->makePartial();
        $arrayUtils->shouldReceive('checkMandatoryKeys')->andReturn(null);
        $orderEndpoint->arrayUtils = $arrayUtils;

        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Parameter "is_shipped" must be a boolean');

        $orderEndpoint->validateStatusData(array(
            'status' => $this->status,
            'is_shipped' => 'oui',
        ));
    }

    public function testValidateStatusDataStatusIsEmpty()
    {
        $orderEndpoint = Mockery::mock(Orders::class)->makePartial();
        $arrayUtils = Mockery::mock(ArrayUtils::class)->makePartial();
        $arrayUtils->shouldReceive('checkMandatoryKeys')->andReturn(null);
        $orderEndpoint->arrayUtils = $arrayUtils;

        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Missing the required parameter "status" when calling orders.sendStatus');

        $orderEndpoint->validateStatusData(array(
            'status' => '',
            'is_shipped' => true,
        ));
    }

    public function testSendStatusOk()
    {
        $this->orderEndpoint->shouldReceive('validateStatusData')->andReturn(null);

        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('setRequestBody')->andReturn($this->requestObject);

        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);
        $this->orderEndpoint->shouldReceive('request')
            ->with(Orders::ORDERS_PATH_V2 . "/{$this->externalId}/status")
            ->once()
            ->andReturn($this->requestObject);

        $this->orderEndpoint->sendStatus($this->externalId, array(
            'status' => $this->status,
            'is_shipped' => true,
        ));
    }

    public function testSendStatusRequestError()
    {
        $this->orderEndpoint->shouldReceive('validateStatusData')->andReturn(null);

        $this->responseMock->shouldReceive('isError')->once()->andReturn(true);
        $this->requestObject->shouldReceive('setRequestBody')->andReturn($this->requestObject);

        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);
        $this->orderEndpoint->shouldReceive('request')
            ->with(Orders::ORDERS_PATH_V2 . "/{$this->externalId}/status")
            ->once()
            ->andReturn($this->requestObject);

        $this->expectException(RequestException::class);
        $this->orderEndpoint->sendStatus($this->externalId, array(
            'status' => $this->status,
            'is_shipped' => true,
        ));

    }

    public function testSendStatusWithException()
    {
        $this->orderEndpoint->shouldReceive('validateStatusData')->andReturn(null);

        $this->responseMock->shouldReceive('isError')->never();
        $this->requestObject->shouldReceive('setRequestBody')->andReturn($this->requestObject);

        $this->requestObject->shouldReceive('post')->andThrow(new RequestException());
        $this->orderEndpoint->shouldReceive('request')
            ->with(Orders::ORDERS_PATH_V2 . "/{$this->externalId}/status")
            ->once()
            ->andReturn($this->requestObject);

        $this->expectException(RequestException::class);
        $this->orderEndpoint->sendStatus($this->externalId, array(
            'status' => $this->status,
            'is_shipped' => true,
        ));
    }

    public function testAddTrackingThrowsAlmaException()
    {
        $this->expectException(AlmaException::class);
        $this->requestObject->shouldReceive('post')->andThrow(new RequestError());
        $this->requestObject->shouldReceive('setRequestBody')->andReturn($this->requestObject);
        $this->orderEndpoint->shouldReceive('request')
            ->with('/v2/orders/123/shipment')
            ->once()
            ->andReturn($this->requestObject);
        $this->orderEndpoint->addTracking('123', 'ups', '123456', 'myUrl');
    }
    public function testAddTrackingThrowsAlmaExceptionForBadData()
    {
        $this->expectException(RequestException::class);
        $this->responseMock->shouldReceive('isError')->once()->andReturn(true);
        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);
        $this->requestObject->shouldReceive('setRequestBody')->andReturn($this->requestObject);
        $this->orderEndpoint->shouldReceive('request')
            ->with('/v2/orders/123/shipment')
            ->once()
            ->andReturn($this->requestObject);
        $this->orderEndpoint->addTracking('123', 'ups', null);
    }


    /**
     * @return void
     * @throws AlmaException
     */
    public function testAddTracking()
    {
        $trackingData = [
            'carrier' => 'UPS',
            'tracking_number' => 'UPS_123456',
            'tracking_url' => 'https://tracking.com'
        ];
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);
        $this->requestObject->shouldReceive('setRequestBody')->with($trackingData)->andReturn($this->requestObject);

        $this->orderEndpoint->shouldReceive('request')
            ->with('/v2/orders/123/shipment')
            ->once()
            ->andReturn($this->requestObject);

        $this->orderEndpoint->addTracking(
            '123',
            $trackingData['carrier'],
            $trackingData['tracking_number'],
            $trackingData['tracking_url']
        );
    }

}
