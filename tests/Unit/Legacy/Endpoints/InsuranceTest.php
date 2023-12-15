<?php

namespace Alma\API\Tests\Unit\Legacy\Endpoints;

use Alma\API\ClientContext;
use Alma\API\Endpoints\Insurance;
use Alma\API\Entities\Insurance\Contract;
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

	protected function setUp()
	{
		$this->clientContext = $this->createMock(ClientContext::class);

	}

	public function testInsuranceEligibilityMethodExist()
	{
		$insurance = new Insurance($this->clientContext);
		$this->assertTrue(method_exists($insurance, 'getInsuranceContract'));
	}

	/**
	 * @dataProvider requestDataProviderRightParams
	 * @return void
	 * @throws ParamsException
	 */
	public function testGetRequestIsCalled($insuranceContractExternalId, $cmsReference, $productPrice)
	{
		$responseMock = Mockery::mock(Response::class);
		$responseMock->shouldReceive('isError')->once()->andReturn(false);

		$requestObject = Mockery::mock(Request::class);
		$requestObject->shouldReceive('get')->once()->andReturn($responseMock);

		$insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
		$insurance->shouldReceive('request')
			->with('/v1/insurance/insurance-contracts/' . $insuranceContractExternalId . '?cms_reference=' . $cmsReference . '&product_price=' . $productPrice)
			->once()
			->andReturn($requestObject)
			;
		$insurance->setClientContext($this->clientContext);

		$insurance->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice);
		Mockery::close();
	}


    /**
     * @dataProvider requestDataProvider
     * @param $insuranceContractExternalId
     * @param $cmsReference
     * @param $productPrice
     * @return void
     * @throws ParamsException
     */
	public function  testGetRequestWithWrongParams($insuranceContractExternalId, $cmsReference, $productPrice)
	{
		$requestObject = Mockery::mock(Request::class);
		$requestObject->shouldNotReceive('get');

		$insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
		$insurance->shouldNotReceive('request');
		$insurance->setClientContext($this->clientContext);
		$this->expectException(ParamsException::class);
		$insurance->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice);
		Mockery::close();
	}

    /**
     * @return void
     * @throws ParamsException
     */
	public function testApiResponseErrorThrowRequestException()
	{
        $insuranceContractExternalId = 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx';
		$cmsReference = '18-24';
        $productPrice = 10000;
		$responseMock = Mockery::mock(Response::class);
		$responseMock->shouldReceive('isError')->once()->andReturn(true);

		$requestObject = Mockery::mock(Request::class)->shouldAllowMockingProtectedMethods();
		$requestObject->shouldReceive('get')->once()->andReturn($responseMock);

		$insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
		$insurance->shouldReceive('request')
			->with('/v1/insurance/insurance-contracts/' . $insuranceContractExternalId . '?cms_reference=' . $cmsReference . '&product_price=' . $productPrice)
			->once()
			->andReturn($requestObject)
		;

		$insurance->setClientContext($this->clientContext);
		$this->expectException(RequestError::class);
		$insurance->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice);
		Mockery::close();
	}

    /**
     * @throws ParamsException
     */
    public function testApiResponse()
    {
        $insuranceContractExternalId = 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx';
        $cmsReference = '1-2';
        $productPrice = 10000;
        $contractExpected = new Contract(
            "insurance_contract_6XxGHbjr51CE5Oly8E2Amx",
         "Alma outillage thermique 3 ans (Vol + casse)",
         1095,
         null,
         null,
         null,
         null,
         null,
         500,
            'files'
        );
        $insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $contract = $insurance->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice);
        $this->assertEquals($contractExpected, $contract);
    }

	public static function requestDataProvider()
	{
		return [
			'Throw exception with cms reference null' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => null,
                'product_price' => '10000'
			],
			'Throw exception with cms reference array' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => ['10','13'],
                'product_price' => '10000'
			],
			'Throw exception with cms reference class' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => new \stdClass(),
                'product_price' => '10000'
			],
			'Throw exception with cms reference bool' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => true,
                'product_price' => '10000'
			],
			'Throw exception with cms reference string and special characters' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => 'Une Str|ng [Avec] des *',
                'product_price' => '10000'
			],
			'Throw exception with cms reference string and spacial characters 2' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => 'alma-%product',
                'product_price' => '10000'
			],
			'Throw exception with cms reference empty string' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => '',
                'product_price' => '10000'
			]
		];
	}
	public static function requestDataProviderRightParams()
	{
		return [
			'call get with cms reference a string' => [
				'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => '1-2',
				'product_price' => '10000'
			],
			'call get with cms reference an integer' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => 18,
                'product_price' => '10000'
			],
			'Call get with cms reference a string and space' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => 'Alma insurance2 product',
                'product_price' => '10000'
			],
			'Call get with cms reference a string and - ' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => 'Alma01-insurance-product',
                'product_price' => '10000'
			]
		];
	}
}
