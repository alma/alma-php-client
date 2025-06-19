<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Endpoint\ConfigurationEndpoint;
use Alma\API\Exceptions\ClientException;
use Alma\API\Exceptions\Endpoint\ConfigurationEndpointException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;

class ConfigurationEndpointTest extends AbstractEndpointSetUp
{
	const URL = "https://www.example.com/integrations/configurations";

    /** @var ConfigurationEndpoint|Mock */
    private $configurationServiceMock;

	public function setUp(): void
	{
        parent::setUp();

        // Mocks
        $this->responseMock = Mockery::mock(Response::class);

        // ConfigurationService
		$this->configurationServiceMock = Mockery::mock(ConfigurationEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
	}

    /**
     * Ensure we can send the integrations configurations URL
     * @throws ConfigurationEndpointException
     */
    public function testSendIntegrationsConfigurationsUrl()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Call
		$this->configurationServiceMock->sendIntegrationsConfigurationsUrl(self::URL);
	}

    /**
     * Ensure we throw a ConfigurationServiceException when the response is an error
     * @throws ConfigurationEndpointException
     */
    public function testSendIntegrationsConfigurationsUrlConfigurationServiceException()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(true);
        $responseMock->shouldReceive('getReasonPhrase')->andReturn('oops');
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // Expectations
		$this->expectException(ConfigurationEndpointException::class);

        // Call
		$this->configurationServiceMock->sendIntegrationsConfigurationsUrl(self::URL);
	}

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws ConfigurationEndpointException
     */
    public function testSendIntegrationsConfigurationsUrlRequestException()
    {
        // Mocks
        $configurationServiceMock = Mockery::mock(ConfigurationEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $configurationServiceMock->shouldReceive('createPutRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(ConfigurationEndpointException::class);
        $configurationServiceMock->sendIntegrationsConfigurationsUrl('url');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws ConfigurationEndpointException
     */
    public function testSendIntegrationsConfigurationsUrlClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(ConfigurationEndpointException::class);
        $this->configurationServiceMock->sendIntegrationsConfigurationsUrl('url');
    }

}
