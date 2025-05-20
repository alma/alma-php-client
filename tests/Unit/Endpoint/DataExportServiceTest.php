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

class DataExportServiceTest extends AbstractEndpointService
{
    const DEFAULT_JSON_RESPONSE = '{"json_key": "json_value"}';
    const DEFAULT_ID = '12345';
    const DEFAULT_STATUS = 'completed';

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
        $responseMock = Mockery::mock(Response::class, [200, [], self::DEFAULT_JSON_RESPONSE])
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
        $responseMock = Mockery::mock(Response::class, [200, [], self::DEFAULT_JSON_RESPONSE])
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
        $responseMock = Mockery::mock(Response::class, [200, [], self::DEFAULT_JSON_RESPONSE])
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
        $responseMock->shouldReceive('getJson')->andReturn(['id' => self::DEFAULT_ID, 'status' => self::DEFAULT_STATUS]);
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // DataExportService
        $result = $this->dataExportsService->create(['type' => 'report']);

        // Assertions
        $this->assertInstanceOf(DataExport::class, $result);
        $this->assertEquals(self::DEFAULT_ID, $result->id);
        $this->assertEquals(self::DEFAULT_STATUS, $result->status);
    }

    /**
     * @throws DataExportServiceException
     */
    public function testFetchSuccess()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->shouldReceive('getJson')->andReturn(['id' => self::DEFAULT_ID, 'status' => self::DEFAULT_STATUS]);
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Call
        $result = $this->dataExportsService->fetch(self::DEFAULT_ID);

        // Assertions
        $this->assertInstanceOf(DataExport::class, $result);
        $this->assertEquals(self::DEFAULT_ID, $result->id);
        $this->assertEquals(self::DEFAULT_STATUS, $result->status);
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
        $result = $this->dataExportsService->download(self::DEFAULT_ID, 'csv');

        // Assertions
        $this->assertEquals('file_content', $result);
    }
}
