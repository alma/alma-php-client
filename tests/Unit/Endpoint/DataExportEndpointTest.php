<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Endpoint\DataExportEndpoint;
use Alma\API\Entities\DataExport;
use Alma\API\Exceptions\ClientException;
use Alma\API\Exceptions\DataExportServiceException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Lib\StreamHelper;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;
use Psr\Log\LoggerInterface;

class DataExportEndpointTest extends AbstractEndpointSetUp
{
    const DEFAULT_JSON_RESPONSE = '{"json_key": "json_value"}';

    const DATA_EXPORT_JSON_RESPONSE = '{
            "complete": false,
            "created": 1747832865,
            "end": 1747832865,
            "id": "export_1213JjthYed5YYk5fQgQZNOzevlY5FvvU7",
            "include_child_accounts": false,
            "merchant": 126928,
            "start": 1696197600,
            "type": "payments",
            "updated": 1747832865
        }';

    const DEFAULT_ID = 'export_1213JjthYed5YYk5fQgQZNOzevlY5FvvU7';

    /** @var Response|Mock */
    protected $responseMock;

    /** @var Response|Mock */
    protected $badResponseMock;

    /** @var DataExportEndpoint|Mock */
    private $dataExportsService;

    public function setUp(): void
    {
        parent::setUp();

        // Mocks
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');

        $this->responseMock = Mockery::mock(Response::class);
        $this->responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->responseMock->shouldReceive('isError')->andReturn(false);
        $this->responseMock->shouldReceive('getBody')->andReturn(self::DATA_EXPORT_JSON_RESPONSE);
        $this->responseMock->shouldReceive('getJson')->andReturn(json_decode(self::DATA_EXPORT_JSON_RESPONSE, true));

        $this->badResponseMock = Mockery::mock(Response::class)
            ->makePartial();
        $this->badResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $this->badResponseMock->shouldReceive('isError')->andReturn(true);
        $this->badResponseMock->shouldReceive('getBody')->andReturn(self::DEFAULT_JSON_RESPONSE);

        // DataExportService
        $this->dataExportsService = Mockery::mock(
            DataExportEndpoint::class,
            [$this->clientMock]
        )->makePartial();
    }

    /**
     * Ensure we can create a DataExport
     * @throws DataExportServiceException
     */
    public function testCreateDataExport()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // DataExportService
        $result = $this->dataExportsService->create('report');

        // Assertions
        $this->assertInstanceOf(DataExport::class, $result);
        $this->assertEquals(self::DEFAULT_ID, $result->id);
    }

    /**
     * Ensure we can catch DataExportServiceException
     * @throws DataExportServiceException
     */
    public function testCreateDataExportDataExportServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(DataExportServiceException::class);

        // Call
        $this->dataExportsService->create('payments');
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws DataExportServiceException
     */
    public function testCreateDataExportRequestException()
    {
        // Mocks
        $dataExportServiceMock = Mockery::mock(DataExportEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $dataExportServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(DataExportServiceException::class);
        $dataExportServiceMock->create('payments');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws DataExportServiceException
     */
    public function testCreateDataExportClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(DataExportServiceException::class);
        $this->dataExportsService->create('payments');
    }

    /**
     * Ensure we can fetch a DataExport
     * @throws DataExportServiceException
     */
    public function testFetchDataExport()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // Call
        $result = $this->dataExportsService->fetch(self::DEFAULT_ID);

        // Assertions
        $this->assertInstanceOf(DataExport::class, $result);
        $this->assertEquals(self::DEFAULT_ID, $result->id);
    }

    /**
     * Ensure we can catch DataExportServiceException
     * @throws DataExportServiceException
     */
    public function testFetchDataExportDataExportServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(DataExportServiceException::class);

        // Call
        $this->dataExportsService->fetch(123);
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws DataExportServiceException
     */
    public function testFetchDataExportRequestException()
    {
        // Mocks
        $dataExportServiceMock = Mockery::mock(DataExportEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $dataExportServiceMock->shouldReceive('createGetRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(DataExportServiceException::class);
        $dataExportServiceMock->fetch(123);
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws DataExportServiceException
     */
    public function testFetchDataExportClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(DataExportServiceException::class);
        $this->dataExportsService->create(123);
    }

    /**
     * Ensure we can download a DataExport
     * @throws DataExportServiceException|ParametersException
     */
    public function testDownloadDataExport()
    {
        // Mocks
        $this->responseMock->shouldReceive('getFile')->andReturn('file_content');
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // Call
        $result = $this->dataExportsService->download(self::DEFAULT_ID, 'csv');

        // Assertions
        $this->assertEquals('file_content', $result);
    }


    /**
     * Ensure we can catch DataExportServiceException
     * @throws DataExportServiceException|ParametersException
     */
    public function testDownloadDataExportDataExportServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(DataExportServiceException::class);

        // Call
        $this->dataExportsService->download(123, 'csv');
    }

    /**
     * Ensure we can catch ParametersException
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
     * Ensure we can catch RequestException
     * @return void
     * @throws DataExportServiceException
     * @throws ParametersException
     */
    public function testDownloadDataExportRequestException()
    {
        // Mocks
        $dataExportServiceMock = Mockery::mock(DataExportEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $dataExportServiceMock->shouldReceive('createGetRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(DataExportServiceException::class);
        $dataExportServiceMock->download(123, 'csv');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws DataExportServiceException|ParametersException
     */
    public function testDownloadDataExportClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(DataExportServiceException::class);
        $this->dataExportsService->download(123, 'csv');
    }
}
