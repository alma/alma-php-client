<?php

namespace Unit\Endpoints;

use Alma\API\Endpoints\Merchants;
use Alma\API\Exceptions\RequestException;
use Alma\API\Request;
use Alma\API\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class MerchantsTest extends TestCase
{
	const URL = "https://www.example.com/integrations/configurations";

	public function setUp(): void
	{
		$this->merchantEndpoint = Mockery::mock(Merchants::class)->makePartial();
		$this->responseMock = Mockery::mock(Response::class);
		$this->requestObject = Mockery::mock(Request::class);
	}

	public function tearDown(): void
	{
		$this->merchantEndpoint = null;
		$this->responseMock = null;
		$this->requestObject = null;
		Mockery::close();
	}

	public function testSendIntegrationsConfigurationsUrlIsOk(){
		$this->responseMock->shouldReceive('isError')->once()->andReturn(false);
		$this->requestObject->shouldReceive('setRequestBody')
			->with(['endpoint_url' => self::URL])
			->andReturn($this->requestObject);

		$this->merchantEndpoint->shouldReceive('request')
			->with(Merchants::ME_PATH . "/integrations/configurations")
			->once()
			->andReturn($this->requestObject);
		$this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);

		$url = self::URL;
		$this->assertNull($this->merchantEndpoint->sendIntegrationsConfigurationsUrl($url));
	}

	public function testSendIntegrationsConfigurationsUrlThrowRequestException(){
		$this->responseMock->shouldReceive('isError')->once()->andReturn(true);
		$this->requestObject->shouldReceive('setRequestBody')
			->with(['endpoint_url' => self::URL])
			->andReturn($this->requestObject);

		$this->merchantEndpoint->shouldReceive('request')
			->with(Merchants::ME_PATH . "/integrations/configurations")
			->once()
			->andReturn($this->requestObject);
		$this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);

		$url = self::URL;
		$this->expectException(RequestException::class);
		$this->merchantEndpoint->sendIntegrationsConfigurationsUrl($url);

	}
}