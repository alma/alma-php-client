<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Endpoint\ConfigurationService;
use Alma\API\Exceptions\ConfigurationServiceException;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;

class ConfigurationServiceTest extends AbstractEndpointServiceTest
{
	const URL = "https://www.example.com/integrations/configurations";

    /** @var ConfigurationService|Mock */
    private $configurationService;

	public function setUp(): void
	{
        parent::setUp();

        // Mocks
        $this->responseMock = Mockery::mock(Response::class);

        // ConfigurationService
		$this->configurationService = Mockery::mock(ConfigurationService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
	}

    /**
     * @throws ConfigurationServiceException
     */
    public function testSendIntegrationsConfigurationsUrlIsOk()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Call
		$this->configurationService->sendIntegrationsConfigurationsUrl(self::URL);
	}

    /**
     * @throws ConfigurationServiceException
     */
    public function testSendIntegrationsConfigurationsUrlThrowConfigurationServiceException()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(true);
        $responseMock->shouldReceive('getReasonPhrase')->andReturn('oops');
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Expectations
		$this->expectException(ConfigurationServiceException::class);

        // Call
		$this->configurationService->sendIntegrationsConfigurationsUrl(self::URL);
	}
}
