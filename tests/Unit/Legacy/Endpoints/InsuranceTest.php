<?php

namespace Alma\API\Tests\Unit\Legacy\Endpoints;

use Alma\API\ClientContext;
use Alma\API\Endpoints\Insurance;
use Alma\API\Entities\Insurance\Contract;
use Alma\API\Entities\Insurance\File;
use Alma\API\Entities\Insurance\Subscription;
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

    /**
     * @return void
     */
	protected function setUp()
	{
		$this->clientContext = $this->createMock(ClientContext::class);
        $this->responseMock = Mockery::mock(Response::class);
        $this->requestObject = Mockery::mock(Request::class);
	}

    /**
     * @return void
     */
	public function testInsuranceEligibilityMethodExist()
	{
		$insurance = new Insurance($this->clientContext);
		$this->assertTrue(method_exists($insurance, 'getInsuranceContract'));
	}

    /**
     * @dataProvider requestDataProviderRightParams
     * @param string $insuranceContractExternalId
     * @param string $cmsReference
     * @param int $productPrice
     * @return void
     * @throws ParamsException
     * @throws RequestError
     */
	public function testGetRequestIsCalled($insuranceContractExternalId, $cmsReference, $productPrice)
	{
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('get')->once()->andReturn($this->responseMock);

		$insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
		$insurance->shouldReceive('request')
			->with('/v1/insurance/insurance-contracts/' . $insuranceContractExternalId . '?cms_reference=' . $cmsReference . '&product_price=' . $productPrice)
			->once()
			->andReturn($this->requestObject);
		$insurance->setClientContext($this->clientContext);

		$insurance->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice);
		Mockery::close();
	}


    /**
     * @dataProvider requestDataProvider
     * @param string $insuranceContractExternalId
     * @param string $cmsReference
     * @param int $productPrice
     * @return void
     * @throws ParamsException
     * @throws RequestError
     */
	public function testGetRequestWithWrongParams($insuranceContractExternalId, $cmsReference, $productPrice)
	{
        $this->requestObject->shouldNotReceive('get');

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
     * @throws RequestError
     */
	public function testApiResponseErrorThrowRequestException()
	{
        $insuranceContractExternalId = 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx';
		$cmsReference = '18-24';
        $productPrice = 10000;
        $this->responseMock->shouldReceive('isError')->once()->andReturn(true);

		$requestObject = Mockery::mock(Request::class)->shouldAllowMockingProtectedMethods();
		$requestObject->shouldReceive('get')->once()->andReturn($this->responseMock);

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
     * @dataProvider requestDataProviderRightParams
     * @param string $insuranceContractExternalId
     * @param string $cmsReference
     * @param int $productPrice
     * @return void
     * @throws ParamsException
     * @throws RequestError
     */
    public function testApiResponse($insuranceContractExternalId, $cmsReference, $productPrice)
    {
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
            [
                new File('Alma mobility 1 an (vol+casse+assistance) - Alma}', 'ipid-document', 'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/I6LK9O3XUNKNZPDTMH58IIK2HKBMRM2MIH-V0YGPECCD5Z20YIQUKXVCZYEU_TJD.pdf/OFXRU1UHY7J0CFO7X0Y24RSDMTG-W5BVB1GZRPPZFPSJRNIGGP2HXR2CEXIPBWZ-.pdf'),
                new File('Alma mobility 1 an (vol+casse+assistance) - Alma}', 'fic-document', 'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/Y-PSWZG6-ADZ9MEY8PAZS2TMAUBXOLU6GYOLDWULMEAJB_VW0RGBKJTPMY7SPASN.pdf/UHSB9KVIGRLHP9DMXRZNCSWUGXCHS9VOW2EHAUNCYM_ANJIE7DOAKVLIH6EEOQYW.pdf'),
                new File('Alma mobility 1 an (vol+casse+assistance) - Alma}', 'notice-document', 'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/JVPHA9RROHB6RPCG9K3VFG4EELBIMALK4QY2JVYEUTBFFT4SP1YN_ZUFXHOYRUSP.pdf/YTBTRJ6C9FFQFNW3234PHJJJT28VZR0FDOXVV0HV1SULI79S3UPSYRX7SZDNX1FX.pdf')
            ]
        );
        $json = '{
            "id": "insurance_contract_6XxGHbjr51CE5Oly8E2Amx",
            "name": "Alma outillage thermique 3 ans (Vol + casse)",
            "protection_days": 1095,
            "description": null,
            "cover_area": null,
            "compensation_area": null,
            "exclusion_area": null,
            "uncovered_area": null,
            "price": 500,
            "files": [
                {
                    "name": "Alma mobility 1 an (vol+casse+assistance) - Alma}",
                    "type": "ipid-document",
                    "public_url": "https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/I6LK9O3XUNKNZPDTMH58IIK2HKBMRM2MIH-V0YGPECCD5Z20YIQUKXVCZYEU_TJD.pdf/OFXRU1UHY7J0CFO7X0Y24RSDMTG-W5BVB1GZRPPZFPSJRNIGGP2HXR2CEXIPBWZ-.pdf"
                },
                {
                    "name": "Alma mobility 1 an (vol+casse+assistance) - Alma}",
                    "type": "fic-document",
                    "public_url": "https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/Y-PSWZG6-ADZ9MEY8PAZS2TMAUBXOLU6GYOLDWULMEAJB_VW0RGBKJTPMY7SPASN.pdf/UHSB9KVIGRLHP9DMXRZNCSWUGXCHS9VOW2EHAUNCYM_ANJIE7DOAKVLIH6EEOQYW.pdf"
                },
                {
                    "name": "Alma mobility 1 an (vol+casse+assistance) - Alma}",
                    "type": "notice-document",
                    "public_url": "https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/JVPHA9RROHB6RPCG9K3VFG4EELBIMALK4QY2JVYEUTBFFT4SP1YN_ZUFXHOYRUSP.pdf/YTBTRJ6C9FFQFNW3234PHJJJT28VZR0FDOXVV0HV1SULI79S3UPSYRX7SZDNX1FX.pdf"
                }
            ]
        }';

        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->responseMock->json = json_decode($json, true);

        $requestObject = Mockery::mock(Request::class)->shouldAllowMockingProtectedMethods();
        $requestObject->shouldReceive('get')->once()->andReturn($this->responseMock);

        $insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $insurance->shouldReceive('request')
            ->with('/v1/insurance/insurance-contracts/' . $insuranceContractExternalId . '?cms_reference=' . $cmsReference . '&product_price=' . $productPrice)
            ->once()
            ->andReturn($requestObject)
        ;
        $insurance->setClientContext($this->clientContext);
        $contract = $insurance->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice);
        $this->assertEquals($contractExpected, $contract);
        Mockery::close();
    }

    /**
     * @dataProvider nonArrayParamDataProvider
     * @throws ParamsException
     */
    public function testSubscriptionThrowExceptionIfNotArrayInParam($nonArrayParam)
    {
        $insurance = new Insurance($this->clientContext);
        $this->expectException(ParamsException::class);
        $insurance->subscription($nonArrayParam);
    }

    /**
     * @return array
     */
    public function nonArrayParamDataProvider()
    {
        return [
            'Test with Null' => [
                null
            ],
            'Test with String' => [
                'my string'
            ],
            'Test with Object' => [
                $this->createMock(Subscription::class)
            ]
        ];
    }

    /**
     * @return array[]
     */
	public static function requestDataProvider()
	{
		return [
			'Throw exception with cms reference null' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => null,
                'product_price' => 10000
			],
            'Throw exception with insurance_contract_external_id null and cms reference null' => [
                'insurance_contract_external_id' => null,
                'cms_reference' => null,
                'product_price' => 10000
            ],
			'Throw exception with cms reference array' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => ['10','13'],
                'product_price' => 10000
			],
			'Throw exception with cms reference class' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => new \stdClass(),
                'product_price' => 10000
			],
			'Throw exception with cms reference bool' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => true,
                'product_price' => 10000
			],
			'Throw exception with cms reference string and special characters' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => 'Une Str|ng [Avec] des *',
                'product_price' => 10000
			],
			'Throw exception with cms reference string and spacial characters 2' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => 'alma-%product',
                'product_price' => 10000
			],
			'Throw exception with cms reference empty string' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => '',
                'product_price' => 10000
			]
		];
	}

    /**
     * @return array[]
     */
	public static function requestDataProviderRightParams()
	{
		return [
			'call get with cms reference a string' => [
				'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => '1-2',
				'product_price' => 10000
			],
			'call get with cms reference an integer' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => 18,
                'product_price' => 10000
			],
			'Call get with cms reference a string and space' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => 'Alma insurance2 product',
                'product_price' => 10000
			],
			'Call get with cms reference a string and - ' => [
                'insurance_contract_external_id' => 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx',
				'cms_reference' => 'Alma01-insurance-product',
                'product_price' => 10000
			]
		];
	}
}
