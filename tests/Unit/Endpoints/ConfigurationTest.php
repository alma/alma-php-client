<?php

namespace Unit\Endpoints;

use Alma\API\Endpoints\Configuration;
use Alma\API\Exceptions\RequestException;
use Alma\API\Request;
use Alma\API\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
	const URL = "https://www.example.com/integrations/configurations";

    private $configurationEndpoint;
    private $responseMock;
    private $requestObject;

	public function setUp(): void
	{
		$this->configurationEndpoint = Mockery::mock(Configuration::class)->makePartial();
		$this->responseMock = Mockery::mock(Response::class);
		$this->requestObject = Mockery::mock(Request::class);
	}

	public function tearDown(): void
	{
		$this->configurationEndpoint = null;
		$this->responseMock = null;
		$this->requestObject = null;
		Mockery::close();
	}

	public function testSendIntegrationsConfigurationsUrlIsOk(){
		$this->responseMock->shouldReceive('isError')->once()->andReturn(false);
		$this->requestObject->shouldReceive('setRequestBody')
			->with(['collect_data_url' => self::URL])
			->andReturn($this->requestObject);

		$this->configurationEndpoint->shouldReceive('request')
			->with(Configuration::CONFIGURATION_PATH . "/api")
			->once()
			->andReturn($this->requestObject);
		$this->requestObject->shouldReceive('put')->once()->andReturn($this->responseMock);

		$this->assertNull($this->configurationEndpoint->sendIntegrationsConfigurationsUrl(self::URL));
	}

	public function testSendIntegrationsConfigurationsUrlThrowRequestException(){
		$this->responseMock->shouldReceive('isError')->once()->andReturn(true);
		$this->requestObject->shouldReceive('setRequestBody')
			->with(['collect_data_url' => self::URL])
			->andReturn($this->requestObject);

		$this->configurationEndpoint->shouldReceive('request')
			->with(Configuration::CONFIGURATION_PATH . "/api")
			->once()
			->andReturn($this->requestObject);
		$this->requestObject->shouldReceive('put')->once()->andReturn($this->responseMock);

		$this->expectException(RequestException::class);
		$this->configurationEndpoint->sendIntegrationsConfigurationsUrl(self::URL);

	}
}