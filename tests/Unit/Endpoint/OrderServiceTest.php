<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Endpoint\OrderService;
use Alma\API\Exceptions\MissingKeyException;
use Alma\API\Exceptions\OrderServiceException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Lib\ArrayUtils;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;
use Psr\Log\LoggerInterface;

class OrderServiceTest extends AbstractEndpointServiceTest
{
    /** @var OrderService|Mock */
    protected $orderService;

    /** @var ArrayUtils|Mock */
    protected $arrayUtilsMock;

    /** @var Response|Mock */
    protected $responseMock;

    public function setUp(): void
    {
        parent::setUp();

        // Mocks
        $this->arrayUtilsMock = Mockery::mock(ArrayUtils::class)->makePartial();
        $this->responseMock = Mockery::mock(Response::class);
        $this->responseMock->errorMessage = 'Exception Error message';
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');

        // OrderService
        $this->orderService = Mockery::mock(OrderService::class, [$this->clientMock])->makePartial();
        $this->orderService->arrayUtils = $this->arrayUtilsMock;
    }

    public function testValidateStatusDataNoOrderData()
    {
        // Expectations
        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Missing in the required parameters (status, is_shipped) when calling orders.sendStatus');

        // Call
        $this->orderService->validateStatusData();
    }

    public function testValidateStatusDataMissingSomeOrderData()
    {
        // Mocks
        $arrayUtils = Mockery::mock(ArrayUtils::class)->makePartial();
        $arrayUtils->shouldReceive('checkMandatoryKeys')->andThrow(new MissingKeyException());

        // OrderService
        $orderService = Mockery::mock(OrderService::class)->makePartial();
        $orderService->arrayUtils = $arrayUtils;

        // Expectations
        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Error in the required parameters (status, is_shipped) when calling orders.sendStatus');

        // Call
        $orderService->validateStatusData(array('status'));
    }

    public function testValidateStatusDataIsShippedNotBool()
    {
        // Mocks
        $arrayUtils = Mockery::mock(ArrayUtils::class)->makePartial();
        $arrayUtils->shouldReceive('checkMandatoryKeys')->andReturn(null);

        // OrderService
        $orderService = Mockery::mock(OrderService::class)->makePartial();
        $orderService->arrayUtils = $arrayUtils;

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
        // Mocks
        $arrayUtils = Mockery::mock(ArrayUtils::class)->makePartial();
        $arrayUtils->shouldReceive('checkMandatoryKeys')->andReturn(null);

        // OrderService
        $orderService = Mockery::mock(OrderService::class)->makePartial();
        $orderService->arrayUtils = $arrayUtils;

        // Expectations
        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('Missing the required parameter "status" when calling orders.sendStatus');

        // Call
        $orderService->validateStatusData(array(
            'status' => '',
            'is_shipped' => true,
        ));
    }

    /**
     * @throws OrderServiceException
     */
    public function testAddTrackingThrowsAlmaException()
    {
        // Mocks
        $this->responseMock->shouldReceive('isError')->once()->andReturn(true);
        $this->responseMock->shouldReceive('getReasonPhrase')->once()->andReturn('oops');
        $this->clientMock->shouldReceive('sendRequest')
            ->once()
            ->andReturn($this->responseMock);

        // Expectations
        $this->expectException(OrderServiceException::class);

        // Call
        $this->orderService->addTracking('123', 'ups', '123456', 'myUrl');
    }

    /**
     * @return void
     * @throws OrderServiceException
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
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->clientMock->shouldReceive('sendRequest')
            ->once()
            ->andReturn($this->responseMock);

        // Call
        $this->orderService->addTracking(
            '123',
            $trackingData['carrier'],
            $trackingData['tracking_number'],
            $trackingData['tracking_url']
        );
    }
}
