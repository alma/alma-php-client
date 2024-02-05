<?php

namespace Alma\API\Tests\Unit\Legacy\Endpoints;

use Alma\API\ClientContext;
use Alma\API\Endpoints\Insurance;
use Alma\API\Entities\Insurance\Contract;
use Alma\API\Entities\Insurance\File;
use Alma\API\Entities\Insurance\Subscriber;
use Alma\API\Entities\Insurance\Subscription;
use Alma\API\Exceptions\MissingKeyException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Lib\ArrayUtils;
use Alma\API\Lib\InsuranceValidator;
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
     * @var Response
     */
    private $responseMock;

    /**
     * @return void
     */
	protected function setUp()
	{
		$this->clientContext = Mockery::mock(ClientContext::class);
        $this->responseMock = Mockery::mock(Response::class);
        $this->requestObject = Mockery::mock(Request::class);
        $this->insuranceMock = Mockery::mock(Insurance::class)->makePartial();
        $this->insuranceValidatorMock = Mockery::mock(InsuranceValidator::class);
        $this->arrayUtilsMock = Mockery::mock(ArrayUtils::class);
	}

    protected function tearDown()
    {
        $this->clientContext = null;
        $this->responseMock = null;
        $this->requestObject = null;
        $this->insuranceMock = null;
        Mockery::close();
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
     * @throws MissingKeyException
     * @throws ParametersException
     * @throws RequestException
     * @throws RequestError
     */
	public function testGetRequestIsCalled($insuranceContractExternalId, $cmsReference, $productPrice)
	{

        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('get')->once()->andReturn($this->responseMock);
        $this->requestObject->shouldReceive('setQueryParams')->once()->andReturn($this->requestObject);
        $this->insuranceMock->shouldReceive('request')->with('/v1/insurance/insurance-contracts/' . $insuranceContractExternalId)->once()->andReturn($this->requestObject);
        $this->insuranceMock->shouldReceive('checkParameters')->once()->with($cmsReference, $insuranceContractExternalId, $productPrice);

        $this->insuranceMock->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice);
	}

    /**
     * @dataProvider requestDataProvider
     * @param string $insuranceContractExternalId
     * @param string $cmsReference
     * @param int $productPrice
     * @return void
     * @throws MissingKeyException
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testThrowParametersExceptionWithWrongParams($insuranceContractExternalId, $cmsReference, $productPrice)
    {
        $this->requestObject->shouldNotReceive('get');
        $this->insuranceMock->shouldNotReceive('request');

        $this->insuranceMock->shouldReceive('checkParameters')->once()->andThrow(ParametersException::class);
        $this->expectException(ParametersException::class);
        $this->insuranceMock->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice);
    }

    /**
     * @return void
     * @throws MissingKeyException
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
	public function testApiResponseErrorThrowRequestException()
	{
        $insuranceContractExternalId = 'insurance_contract_6XxGHbjr51CE5Oly8E2Amx';
		$cmsReference = '18-24';
        $productPrice = 10000;
        $this->responseMock->shouldReceive('isError')->once()->andReturn(true);

		$requestObject = Mockery::mock(Request::class)->shouldAllowMockingProtectedMethods();
		$requestObject->shouldReceive('get')->once()->andReturn($this->responseMock);
        $requestObject->shouldReceive('setQueryParams')->once()->andReturn($requestObject);


        $this->insuranceMock->shouldReceive('request')
			->with('/v1/insurance/insurance-contracts/' . $insuranceContractExternalId)
			->once()
			->andReturn($requestObject)
		;

        $this->insuranceMock->setClientContext($this->clientContext);
        $this->insuranceMock->shouldReceive('checkParameters')->once();
		$this->expectException(RequestException::class);
        $this->insuranceMock->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice);
	}

    /**
     * @dataProvider requestDataProviderRightParams
     * @param string $insuranceContractExternalId
     * @param string $cmsReference
     * @param int $productPrice
     * @return void
     * @throws MissingKeyException
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testApiResponseInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice)
    {
        $files = [
            new File('Alma mobility 1 an (vol+casse+assistance) - Alma}', 'ipid-document', 'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/I6LK9O3XUNKNZPDTMH58IIK2HKBMRM2MIH-V0YGPECCD5Z20YIQUKXVCZYEU_TJD.pdf/OFXRU1UHY7J0CFO7X0Y24RSDMTG-W5BVB1GZRPPZFPSJRNIGGP2HXR2CEXIPBWZ-.pdf'),
            new File('Alma mobility 1 an (vol+casse+assistance) - Alma}', 'fic-document', 'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/Y-PSWZG6-ADZ9MEY8PAZS2TMAUBXOLU6GYOLDWULMEAJB_VW0RGBKJTPMY7SPASN.pdf/UHSB9KVIGRLHP9DMXRZNCSWUGXCHS9VOW2EHAUNCYM_ANJIE7DOAKVLIH6EEOQYW.pdf'),
            new File('Alma mobility 1 an (vol+casse+assistance) - Alma}', 'notice-document', 'https://object-storage-s3-staging.s3.fr-par.scw.cloud/contracts/43acb66c-4b24-42d2-864a-24b4ade33e81/JVPHA9RROHB6RPCG9K3VFG4EELBIMALK4QY2JVYEUTBFFT4SP1YN_ZUFXHOYRUSP.pdf/YTBTRJ6C9FFQFNW3234PHJJJT28VZR0FDOXVV0HV1SULI79S3UPSYRX7SZDNX1FX.pdf')
        ];
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
            $files
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

        $this->requestObject->shouldReceive('get')->once()->andReturn($this->responseMock);
        $this->requestObject->shouldReceive('setQueryParams')->once()->andReturn($this->requestObject);
        $this->insuranceMock->shouldReceive('request')->with('/v1/insurance/insurance-contracts/' . $insuranceContractExternalId)->once()->andReturn($this->requestObject);
        $this->insuranceMock->shouldReceive('checkParameters')->once()->with($cmsReference, $insuranceContractExternalId, $productPrice);

        $this->assertEquals($contractExpected, $this->insuranceMock->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice));
    }

    /**
     * @return void
     */
    public function testInsuranceSubscriptionMethodExist()
    {
        $insurance = new Insurance($this->clientContext);
        $this->assertTrue(method_exists($insurance, 'subscription'));
    }

    /**
     * @dataProvider nonArrayParamDataProvider
     * @param $nonArrayParam
     * @param $nonStringPaymentId
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testSubscriptionThrowExceptionIfNotArrayInParam($nonArrayParam, $nonStringPaymentId)
    {
        $insurance = new Insurance($this->clientContext);
        $this->expectException(ParametersException::class);
        $insurance->subscription($nonArrayParam, $nonStringPaymentId);
    }

    /**
     * @dataProvider subscriptionDataProvider
     * @param $subscriptionArray
     * @return void
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testSubscriptionThrowExceptionRequestError($subscriptionArray)
    {
        $this->responseMock->shouldReceive('isError')->once()->andReturn(true);
        $this->responseMock->json = $subscriptionArray;

        $requestObject = Mockery::mock(Request::class);
        $requestObject->shouldReceive('setRequestBody')->andReturn($requestObject);
        $requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);

        $insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $insurance->shouldReceive('request')
            ->with('/v1/insurance/subscriptions')
            ->once()
            ->andReturn($requestObject);
        $insurance->setClientContext($this->clientContext);
        $this->expectException(RequestException::class);
        $insurance->subscription($subscriptionArray);
    }

    /**
     * @dataProvider subscriptionDataProvider
     * @param $subscriptionArray
     * @param $paymentId
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testSubscriptionGetRequestCall($subscriptionArray, $paymentId)
    {
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('setRequestBody')->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);

        $insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $insurance->shouldReceive('request')
            ->with('/v1/insurance/subscriptions')
            ->once()
            ->andReturn($this->requestObject);
        $insurance->setClientContext($this->clientContext);
        $insurance->subscription($subscriptionArray, $paymentId);
    }

    /**
     * @return void
     */
    public function testSubscriptionMethodExist()
    {
        $insurance = new Insurance($this->clientContext);
        $this->assertTrue(method_exists($insurance, 'getSubscription'));
    }

    public function testGetSubscriptionRequestIsCalled()
    {
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('setQueryParams')->once()->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('get')->once()->andReturn($this->responseMock);
        $this->insuranceMock->shouldReceive('request')
            ->with('/v1/insurance/subscriptions')
            ->once()
            ->andReturn($this->requestObject);

        $this->insuranceMock->getSubscription();
    }

    /**
     * @return array
     */
    public function nonArrayParamDataProvider()
    {
        return [
            'Test with Null and payment id valid' => [
                null,
                'payment_xxx'
            ],
            'Test with String and payment id valid' => [
                'my string',
                'payment_xxx'
            ],
            'Test with Object and payment id valid' => [
                $this->createMock(Subscription::class),
                'payment_xxx'
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function subscriptionDataProvider()
    {
        return [
            'Test with right data and without payment id' => [
                [
                    new Subscription(
                        'insurance_contract_6VU1zZ5AKfy6EejiNxmLXh',
                        '19',
                        1312,
                        new Subscriber(
                            'mathis.dupuy@almapay.com',
                            '+33622484646',
                            'sub1',
                            'sub1',
                            'adr1',
                            'adr1',
                            'adr1',
                            'adr1',
                            'adr1',
                            null
                        ),
                        'cancelUrl'
                    ),
                    new Subscription(
                        'insurance_contract_3vt2jyvWWQc9wZCmWd1KtI',
                        '17-35',
                        1312,
                        new Subscriber(
                            'mathis.dupuy@almapay.com',
                            '+33622484646',
                            'sub2',
                            'sub2',
                            'adr2',
                            'adr2',
                            'adr2',
                            'adr2',
                            'adr2',
                            '1988-08-22'
                        ),
                        'cancelUrl'
                    )
                ],
                null
            ],
            'Test with right data and payment id' => [
                [
                    new Subscription(
                        'insurance_contract_6VU1zZ5AKfy6EejiNxmLXh',
                        '19',
                        1312,
                        new Subscriber(
                            'mathis.dupuy@almapay.com',
                            '+33622484646',
                            'sub1',
                            'sub1',
                            'adr1',
                            'adr1',
                            'adr1',
                            'adr1',
                            'adr1',
                            null
                        ),
                        'cancelUrl'
                    ),
                    new Subscription(
                        'insurance_contract_3vt2jyvWWQc9wZCmWd1KtI',
                        '17-35',
                        1312,
                        new Subscriber(
                            'mathis.dupuy@almapay.com',
                            '+33622484646',
                            'sub2',
                            'sub2',
                            'adr2',
                            'adr2',
                            'adr2',
                            'adr2',
                            'adr2',
                            '1988-08-22'
                        ),
                        'cancelUrl'
                    )
                ],
                'payment_id' => 'payment_11xlpX9QQYhd3xZVzNMrtdKw4myV7QET7X'
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
