<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Endpoint\DataExportService;
use Alma\API\Entities\DataExport;
use Alma\API\Exceptions\DataExportServiceException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;
use Psr\Log\LoggerInterface;

class DataExportServiceTest extends AbstractEndpointServiceTest
{
    /** @var DataExportService|Mock */
    private $dataExportsService;

    public function setUp(): void
    {
        parent::setUp();

        // Mocks
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');
        $this->responseMock = Mockery::mock(Response::class);

        // DataExportService
        $this->dataExportsService = Mockery::mock(
            DataExportService::class,
            [$this->clientMock]
        )->makePartial();
    }

    /**
     * @throws DataExportServiceException
     */
    public function testCreateDataExportPostThrowDataExportServiceException()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class, [200, [], '{"json_key": "json_value"}'])
            ->makePartial();
        $responseMock->shouldReceive('isError')->andReturn(true);
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Expectations
        $this->expectException(DataExportServiceException::class);

        // Call
        $this->dataExportsService->create(['type' => 'payments', 'include_child_accounts' => false]);
    }

    /**
     * @throws DataExportServiceException
     */
    public function testFetchDataExportGetThrowDataExportServiceException()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class, [200, [], '{"json_key": "json_value"}'])
            ->makePartial();
        $responseMock->shouldReceive('isError')->andReturn(true);
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Expectations
        $this->expectException(DataExportServiceException::class);

        // Call
        $this->dataExportsService->fetch(123);
    }

    /**
     * @throws DataExportServiceException|ParametersException
     */
    public function testDownloadDataExportGetThrowDataExportServiceException()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class, [200, [], '{"json_key": "json_value"}'])
            ->makePartial();
        $responseMock->shouldReceive('isError')->andReturn(true);
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Expectations
        $this->expectException(DataExportServiceException::class);

        // Call
        $this->dataExportsService->download(123, 'csv');
    }

    /**
     * @throws DataExportServiceException|ParametersException
     */
    public function testDownloadDataExportInvalidFormat()
    {
        // Expectations
        $this->expectException(ParametersException::class);

        // Call
        $this->dataExportsService->download(123, 'pdf');
    }

    /**
     * @throws DataExportServiceException
     */
    public function testCreateSuccess()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->shouldReceive('getJson')->andReturn(['id' => '12345', 'status' => 'completed']);
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // DataExportService
        $result = $this->dataExportsService->create(['type' => 'report']);

        // Assertions
        $this->assertInstanceOf(DataExport::class, $result);
        $this->assertEquals('12345', $result->id);
        $this->assertEquals('completed', $result->status);
    }

    /**
     * @throws DataExportServiceException
     */
    public function testFetchSuccess()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->shouldReceive('getJson')->andReturn(['id' => '12345', 'status' => 'completed']);
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Call
        $result = $this->dataExportsService->fetch('12345');

        // Assertions
        $this->assertInstanceOf(DataExport::class, $result);
        $this->assertEquals('12345', $result->id);
        $this->assertEquals('completed', $result->status);
    }

    /**
     * @throws DataExportServiceException|ParametersException
     */
    public function testDownloadSuccess()
    {
        // Mocks
        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->shouldReceive('getFile')->andReturn('file_content');
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Call
        $result = $this->dataExportsService->download('12345', 'csv');

        // Assertions
        $this->assertEquals('file_content', $result);
    }
}
