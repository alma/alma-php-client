<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Client;
use Alma\API\Configuration;
use Alma\API\Response;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\Mock;

abstract class AbstractEndpointServiceTest extends MockeryTestCase
{
    /** @var Client|Mock Default Client Mock  */
    protected $clientMock;

    /** @var Response|Mock Default Response Mock  */
    protected $responseMock;

    /**
     */
    protected function setUp(): void {

        // Params
        $configuration = [
            'auth' => ['api_key' => 'sk_test_xxxxxxxxxxxx'],
            'base_uri' => 'https://api.mockery.getalma.eu'
        ];

        // Mocks
        $this->responseMock = Mockery::mock(Response::class);
        $this->responseMock->shouldReceive('isError')->andReturn(false);

        $this->clientMock = Mockery::mock(Client::class, [$configuration]);
        $this->clientMock->shouldReceive('getConfig')->andReturn(new Configuration($configuration));
    }

    public function tearDown(): void
    {
        Mockery::close();
    }
}