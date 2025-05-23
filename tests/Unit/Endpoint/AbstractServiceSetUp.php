<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\CurlClient;
use Alma\API\ClientConfiguration;
use Alma\API\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;

abstract class AbstractServiceSetUp extends MockeryTestCase
{
    /** @var CurlClient|Mock Default Client Mock  */
    protected $clientMock;

    /** @var Response|Mock Default Response Mock  */
    protected $responseMock;

    /** @var Response|Mock Default Bad Response Mock  */
    protected $badResponseMock;

    /**
     */
    protected function setUp(): void {

        // Params
        $configuration = new ClientConfiguration(
            'https://api.mockery.getalma.eu',
            'sk_test_xxxxxxxxxxxx'
        );

        // Mocks
        $this->responseMock = Mockery::mock(Response::class);
        $this->responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->responseMock->shouldReceive('isError')->andReturn(false);

        $this->badResponseMock = Mockery::mock(Response::class);
        $this->badResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badResponseMock->shouldReceive('isError')->andReturn(true);

        $this->clientMock = Mockery::mock(CurlClient::class, [$configuration]);
        $this->clientMock->shouldReceive('getConfig')->andReturn($configuration);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}