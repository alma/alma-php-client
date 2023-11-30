<?php

namespace Alma\API\Tests\Unit\PHP8_1\Endpoints;

use Alma\API\ClientContext;
use Alma\API\Endpoints\Insurance;
use Alma\API\Exceptions\ParamsException;
use Alma\API\Request;
use Alma\API\RequestError;
use Alma\API\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class InsuranceTest extends TestCase
{
	/**
	 * @var ClientContext
	 */
	private $clientContext;

	protected function setUp(): void
	{
		$this->clientContext = $this->createMock(ClientContext::class);

	}

	public function testInsuranceEligibilityMethodExist(): void
	{
		$insurance = new Insurance($this->clientContext);
		$this->assertTrue(method_exists($insurance, 'getInsuranceContracts' ));
	}

	/**
	 * @dataProvider requestDataProviderRightParams
	 * @return void
	 * @throws ParamsException
	 */
	public function testGetRequestIsCalled($productId): void
	{

		$responseMock = Mockery::mock(Response::class);
		$responseMock->shouldReceive('isError')->once()->andReturn(false);

		$requestObject = Mockery::mock(Request::class);
		$requestObject->shouldReceive('get')->once()->andReturn($responseMock);

		$insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
		$insurance->shouldReceive('request')
			->with('/v1/insurance/insurance-contracts?cms_product_id='.$productId)
			->once()
			->andReturn($requestObject)
			;
		$insurance->setClientContext($this->clientContext);

		$insurance->getInsuranceContracts($productId);
		Mockery::close();
	}


	/**
	 * @dataProvider requestDataProvider
	 * @param $productId
	 * @return void
	 */
	public function  testGetRequestWithWrongParams($productId):void
	{
		$requestObject = Mockery::mock(Request::class);
		$requestObject->shouldNotReceive('get');

		$insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
		$insurance->shouldNotReceive('request');
		$insurance->setClientContext($this->clientContext);
		$this->expectException(ParamsException::class);
		$insurance->getInsuranceContracts($productId);
		Mockery::close();
	}

	public function testApiResponseErrorThrowRequestException()
	{
		$productId = '18-24';
		$responseMock = Mockery::mock(Response::class);
		$responseMock->shouldReceive('isError')->once()->andReturn(true);

		$requestObject = Mockery::mock(Request::class)->shouldAllowMockingProtectedMethods();
		$requestObject->shouldReceive('get')->once()->andReturn($responseMock);

		$insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
		$insurance->shouldReceive('request')
			->with('/v1/insurance/insurance-contracts?cms_product_id='.$productId)
			->once()
			->andReturn($requestObject)
		;

		$insurance->setClientContext($this->clientContext);
		$this->expectException(RequestError::class);
		$insurance->getInsuranceContracts($productId);
		Mockery::close();
	}

	public static function requestDataProvider(): array
	{
		return [
			'Throw exception with null' => [
				'product_id' => null
			],
			'Throw exception with array' => [
				'product_id' => ['10','13']
			],
			'Throw exception with class' => [
				'product_id' => new \stdClass()
			],
			'Throw exception with bool' => [
				'product_id' => true
			],
			'Throw exception with string and special characters' => [
				'product_id' => 'Une Str|ng [Avec] des *'
			],
			'Throw exception with string and spacial characters 2' => [
				'product_id' => 'alma-%product'
			],
			'Throw exception with empty string' => [
				'product_id' => ''
			]
		];
	}
	public static function requestDataProviderRightParams(): array
	{
		return [
			'call get with a string' => [
				'product_id' => '18'
			],
			'call get with an integer' => [
				'product_id' => 18
			],
			'Call get with a string and space' => [
				'product_id' => 'Alma insurance2 product'
			],
			'Call get with a string and - ' => [
				'product_id' => 'Alma01-insurance-product'
			]
		];
	}

}
