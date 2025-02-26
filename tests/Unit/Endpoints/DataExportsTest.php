<?php

namespace Alma\API\Tests\Unit\Endpoints;

use Alma\API\ClientContext;
use Alma\API\Endpoints\DataExports;
use Alma\API\Entities\DataExport;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Request;
use Alma\API\RequestError;
use Alma\API\Response;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class DataExportsTest extends TestCase
{
    private $clientContext;
    private $dataExportsEndpoint;
    private $requestObject;
    private $responseMock;

    public function setUp(): void
    {
        $this->clientContext = Mockery::mock(ClientContext::class);
        $this->dataExportsEndpoint = Mockery::mock(DataExports::class)->makePartial();
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');
        $this->requestObject = Mockery::mock(Request::class);
        $this->responseMock = Mockery::mock(Response::class);
        $this->clientContext->logger = $loggerMock;
        $this->dataExportsEndpoint->setClientContext($this->clientContext);
    }

    public function tearDown(): void
    {
        $this->dataExportsEndpoint = null;
        $this->responseMock = null;
        $this->requestObject = null;
        $this->clientContext = null;
        Mockery::close();
    }

    /**
     * @throws RequestError
     * @throws RequestException
     */
    public function testCreateDataExportPostThrowRequestException()
    {
        $this->dataExportsEndpoint->shouldReceive('request')
            ->with('/v1/data-exports')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('setRequestBody')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('post')
            ->once()
            ->andThrow(new RequestException('Request error', null, null));

        $this->expectException(RequestException::class);
        $this->dataExportsEndpoint->create(['type' => 'payments', 'include_child_accounts' => false]);
    }

    /**
     * @throws RequestError
     * @throws RequestException
     */
    public function testFetchDataExportGetThrowRequestException()
    {
        $this->dataExportsEndpoint->shouldReceive('request')
            ->with('/v1/data-exports/123')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('get')
            ->once()
            ->andThrow(new RequestException('Request error', null, null));

        $this->expectException(RequestException::class);
        $this->dataExportsEndpoint->fetch(123);
    }

    /**
     * @throws RequestException|ParametersException|RequestError
     */
    public function testDownloadDataExportGetThrowRequestException()
    {
        $this->dataExportsEndpoint->shouldReceive('request')
            ->with('/v1/data-exports/123')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('setQueryParams')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('get')
            ->once()
            ->andThrow(new RequestException('Request error', null, null));

        $this->expectException(RequestException::class);
        $this->dataExportsEndpoint->download(123, 'csv');
    }

    /**
     * @throws RequestException|ParametersException|RequestError
     */
    public function testDownloadDataExportInvalidFormat()
    {
        $this->expectException(ParametersException::class);
        $this->dataExportsEndpoint->download(123, 'pdf');
    }

    /**
     * @throws RequestException|RequestError
     */
    public function testCreateSuccess()
    {
        $this->dataExportsEndpoint->shouldReceive('request')
            ->with('/v1/data-exports')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('setRequestBody')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('post')
            ->once()
            ->andReturn($this->responseMock);

        $this->responseMock->shouldReceive('isError')->andReturn(false);
        $this->responseMock->json = ['id' => '12345', 'status' => 'completed'];

        $result = $this->dataExportsEndpoint->create(['type' => 'report']);

        $this->assertInstanceOf(DataExport::class, $result);
        $this->assertEquals('12345', $result->id);
        $this->assertEquals('completed', $result->status);
    }

    /**
     * @throws RequestException|RequestError
     */
    public function testFetchSuccess()
    {
        $this->dataExportsEndpoint->shouldReceive('request')
            ->with('/v1/data-exports/12345')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('get')
            ->once()
            ->andReturn($this->responseMock);

        $this->responseMock->shouldReceive('isError')->andReturn(false);
        $this->responseMock->json = ['id' => '12345', 'status' => 'completed'];

        $result = $this->dataExportsEndpoint->fetch('12345');

        $this->assertInstanceOf(DataExport::class, $result);
        $this->assertEquals('12345', $result->id);
        $this->assertEquals('completed', $result->status);
    }

    /**
     * @throws RequestException|ParametersException|RequestError
     */
    public function testDownloadSuccess()
    {
        $this->dataExportsEndpoint->shouldReceive('request')
            ->with('/v1/data-exports/12345')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('setQueryParams')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('get')
            ->once()
            ->andReturn($this->responseMock);

        $this->responseMock->shouldReceive('isError')->andReturn(false);
        $this->responseMock->data = 'file_content';

        $result = $this->dataExportsEndpoint->download('12345', 'csv');

        $this->assertEquals('file_content', $result);
    }
}