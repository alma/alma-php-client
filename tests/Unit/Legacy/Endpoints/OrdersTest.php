<?php

namespace Alma\API\Tests\Unit\Legacy\Endpoints;


use Alma\API\ClientContext;
use Alma\API\Endpoints\Orders;

use Alma\API\Exceptions\MissingKeyException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Lib\ArrayUtils;
use Alma\API\Request;
use Alma\API\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class OrdersTest extends TestCase
{

    /**
     * @var \Mockery\Mock|(\Mockery\MockInterface&Orders)
     */
    protected $orderEndpoint;

    public function setUp()
    {
        $this->clientContext = \Mockery::mock(ClientContext::class);
        $this->orderEndpoint = \Mockery::mock(Orders::class)->makePartial();
        $this->arrayUtils = \Mockery::mock(ArrayUtils::class)->makePartial();
        $this->responseMock = \Mockery::mock(Response::class);
        $this->requestObject = \Mockery::mock(Request::class);
        $loggerMock = \Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');

        $this->orderEndpoint->arrayUtils = $this->arrayUtils;
        $this->externalId  = 'Mon external Id';
        $this->label = 'Mon Label - 1';
        $this->clientContext->logger = $loggerMock;
        $this->orderEndpoint->setClientContext($this->clientContext);

    }
    public function testValidateStatusDataNoOrderData()
    {
        $this->expectException(ParametersException::class);
        $this->expectExceptionCode('204');
        $this->expectExceptionMessage('Missing in the required parameters (label, is_shipped) when calling orders.sendStatus');
        $this->orderEndpoint->validateStatusData();
    }

    public function testValidateStatusDataMissingSomeOrderData()
    {
        $orderEndpoint = \Mockery::mock(Orders::class)->makePartial();
        $arrayUtils = \Mockery::mock(ArrayUtils::class)->makePartial();
        $arrayUtils->shouldReceive('checkMandatoryKeys')->andThrow(new MissingKeyException());
        $orderEndpoint->arrayUtils = $arrayUtils;

        $this->expectException(ParametersException::class);
        $this->expectExceptionCode('400');
        $this->expectExceptionMessage('Error in the required parameters (label, is_shipped) when calling orders.sendStatus');
        $orderEndpoint->validateStatusData(array('label'));
    }

    public function testValidateStatusDataIsShippedNotBool()
    {
        $orderEndpoint = \Mockery::mock(Orders::class)->makePartial();
        $arrayUtils = \Mockery::mock(ArrayUtils::class)->makePartial();
        $arrayUtils->shouldReceive('checkMandatoryKeys')->andReturn(null);
        $orderEndpoint->arrayUtils = $arrayUtils;

        $this->expectException(ParametersException::class);
        $this->expectExceptionCode('400');
        $this->expectExceptionMessage('Parameter "is_shipped" must be a boolean');

        $orderEndpoint->validateStatusData(array(
            'label' => $this->label,
            'is_shipped' => 'oui',
        ));
    }

    public function testValidateStatusDataLabelIsEmpty()
    {
        $orderEndpoint = \Mockery::mock(Orders::class)->makePartial();
        $arrayUtils = \Mockery::mock(ArrayUtils::class)->makePartial();
        $arrayUtils->shouldReceive('checkMandatoryKeys')->andReturn(null);
        $orderEndpoint->arrayUtils = $arrayUtils;

        $this->expectException(ParametersException::class);
        $this->expectExceptionCode('400');
        $this->expectExceptionMessage('Missing the required parameter "label" when calling orders.sendStatus');

        $orderEndpoint->validateStatusData(array(
            'label' => '',
            'is_shipped' => true,
        ));
    }

    public function testSendStatusOK()
    {
        $this->orderEndpoint->shouldReceive('validateStatusData')->andReturn(null);

        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('setRequestBody')->andReturn($this->requestObject);

        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);
        $this->orderEndpoint->shouldReceive('request')
            ->with(Orders::ORDERS_PATH_V2 .  "/{$this->externalId}/status")
            ->once()
            ->andReturn($this->requestObject);

        $this->orderEndpoint->sendStatus($this->externalId  , array(
            'label' => $this->label,
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
            ->with(Orders::ORDERS_PATH_V2 .  "/{$this->externalId}/status")
            ->once()
            ->andReturn($this->requestObject);

        $this->expectException(RequestException::class);
        $this->orderEndpoint->sendStatus($this->externalId  , array(
            'label' => $this->label,
            'is_shipped' => true,
        ));

    }
    public function testSendStatusWithException()
    {
        $this->orderEndpoint->shouldReceive('validateStatusData')->andReturn(null);

        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('setRequestBody')->andReturn($this->requestObject);

        $this->requestObject->shouldReceive('post')->andThrow(new RequestException());
        $this->orderEndpoint->shouldReceive('request')
            ->with(Orders::ORDERS_PATH_V2 .  "/{$this->externalId}/status")
            ->once()
            ->andReturn($this->requestObject);

        $this->expectException(RequestException::class);
        $this->orderEndpoint->sendStatus($this->externalId  , array(
            'label' => $this->label,
            'is_shipped' => true,
        ));
    }


    public function tearDown()
    {
        $this->orderEndpoint = null;
        $this->arrayUtils = null;
        $this->responseMock = null;
        $this->requestObject = null;
    }

}
