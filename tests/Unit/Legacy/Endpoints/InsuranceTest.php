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
use Psr\Log\LoggerInterface;

class InsuranceTest extends TestCase
{
    const INSURANCE_SUBSCRIPTIONS_PATH = '/v1/insurance/subscriptions';
    const INSURANCE_CONTRACTS_PATH = '/v1/insurance/insurance-contracts/';
    const TEST_PHONENUMBER = '+33601010101';
    const TEST_EMAIL = 'test@almapay.com';
    const TEST_CMSREFERENCE = '17-35';
    const TEST_BIRTHDATE = '1988-08-22';
    const INSURANCE_CUSTOMER_CART_PATH = '/v1/insurance/customer-cart';
    /**
     * @var ClientContext
     */
    private $clientContext;
    /**
     * @var Response
     */
    private $responseMock;
    /**
     * @var Request
     */
    private $requestObject;
    /**
     * @var Insurance
     */
    private $insuranceMock;

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
                        1235,
                        '19',
                        1312,
                        new Subscriber(
                            self::TEST_EMAIL,
                            self::TEST_PHONENUMBER,
                            'lastname',
                            'firstname',
                            'address1',
                            'address2',
                            'zipcode',
                            'city',
                            'country',
                            null
                        ),
                        'cancelUrl'
                    ),
                    new Subscription(
                        'insurance_contract_3vt2jyvWWQc9wZCmWd1KtI',
                        1568,
                        self::TEST_CMSREFERENCE,
                        1312,
                        new Subscriber(
                            self::TEST_EMAIL,
                            self::TEST_PHONENUMBER,
                            'last',
                            'first',
                            'adr1',
                            'adr2',
                            'zip',
                            'city',
                            'country',
                            self::TEST_BIRTHDATE
                        ),
                        'cancelUrl'
                    )
                ],
                null,
                [
                    'subscriptions' => [
                        [
                            'insurance_contract_id' => 'insurance_contract_6VU1zZ5AKfy6EejiNxmLXh',
                            'amount' => 1235,
                            'cms_reference' => '19',
                            'product_price' => 1312,
                            'cms_callback_url' => 'cancelUrl',
                            'subscriber' => [
                                'email' => self::TEST_EMAIL,
                                'phone_number' => self::TEST_PHONENUMBER,
                                'last_name' => 'lastname',
                                'first_name' => 'firstname',
                                'birthdate' => null,
                                'address' => [
                                    'address_line_1' => 'address1',
                                    'address_line_2' => 'address2',
                                    'zip_code' => 'zipcode',
                                    'city' => 'city',
                                    'country' => 'country',
                                ]
                            ],
                        ],
                        [
                            'insurance_contract_id' => 'insurance_contract_3vt2jyvWWQc9wZCmWd1KtI',
                            'amount' => 1568,
                            'cms_reference' => self::TEST_CMSREFERENCE,
                            'product_price' => 1312,
                            'cms_callback_url' => 'cancelUrl',
                            'subscriber' => [
                                'email' => self::TEST_EMAIL,
                                'phone_number' => self::TEST_PHONENUMBER,
                                'last_name' => 'last',
                                'first_name' => 'first',
                                'birthdate' => self::TEST_BIRTHDATE,
                                'address' => [
                                    'address_line_1' => 'adr1',
                                    'address_line_2' => 'adr2',
                                    'zip_code' => 'zip',
                                    'city' => 'city',
                                    'country' => 'country',
                                ]
                            ],
                        ]
                    ]
                ]
            ],
            'Test with right data and payment id' => [
                [
                    new Subscription(
                        'insurance_contract_6VU1zZ5AKfy6EejiNxmLXh',
                        1235,
                        '19',
                        1312,
                        new Subscriber(
                            self::TEST_EMAIL,
                            self::TEST_PHONENUMBER,
                            'lastname',
                            'firstname',
                            'address1',
                            'address2',
                            'zipcode',
                            'city',
                            'country',
                            null
                        ),
                        'cancelUrl'
                    ),
                    new Subscription(
                        'insurance_contract_3vt2jyvWWQc9wZCmWd1KtI',
                        1568,
                        self::TEST_CMSREFERENCE,
                        1312,
                        new Subscriber(
                            self::TEST_EMAIL,
                            self::TEST_PHONENUMBER,
                            'last',
                            'first',
                            'adr1',
                            'adr2',
                            'zip',
                            'city',
                            'country',
                            self::TEST_BIRTHDATE
                        ),
                        'cancelUrl'
                    )
                ],
                'payment_id' => 'payment_11xlpX9QQYhd3xZVzNMrtdKw4myV7QET7X',
                [
                    'subscriptions' => [
                        [
                            'insurance_contract_id' => 'insurance_contract_6VU1zZ5AKfy6EejiNxmLXh',
                            'amount' => 1235,
                            'cms_reference' => '19',
                            'product_price' => 1312,
                            'cms_callback_url' => 'cancelUrl',
                            'subscriber' => [
                                'email' => self::TEST_EMAIL,
                                'phone_number' => self::TEST_PHONENUMBER,
                                'last_name' => 'lastname',
                                'first_name' => 'firstname',
                                'birthdate' => null,
                                'address' => [
                                    'address_line_1' => 'address1',
                                    'address_line_2' => 'address2',
                                    'zip_code' => 'zipcode',
                                    'city' => 'city',
                                    'country' => 'country',
                                ]
                            ],
                        ],
                        [
                            'insurance_contract_id' => 'insurance_contract_3vt2jyvWWQc9wZCmWd1KtI',
                            'amount' => 1568,
                            'cms_reference' => self::TEST_CMSREFERENCE,
                            'product_price' => 1312,
                            'cms_callback_url' => 'cancelUrl',
                            'subscriber' => [
                                'email' => self::TEST_EMAIL,
                                'phone_number' => self::TEST_PHONENUMBER,
                                'last_name' => 'last',
                                'first_name' => 'first',
                                'birthdate' => self::TEST_BIRTHDATE,
                                'address' => [
                                    'address_line_1' => 'adr1',
                                    'address_line_2' => 'adr2',
                                    'zip_code' => 'zip',
                                    'city' => 'city',
                                    'country' => 'country',
                                ]
                            ],
                        ]
                    ]
                ]
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
                'cms_reference' => ['10', '13'],
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

    /**
     * @return array
     */
    public function nonArrayParamGetSubscriptionDataProvider()
    {
        return [
            'Test with Null' => [
                null
            ],
            'Test with String' => [
                'my string'
            ],
            'Test with Object' => [
                $this->createMock(\stdClass::class)
            ],
            'Test with Integer' => [
                10
            ],
            'Test with Boolean' => [
                true
            ]
        ];
    }

    /**
     * @return array[]
     */
    public function getSubscriptionsRightParamDataProvider()
    {
        return [
            'Test with 1 subscription id' => [
                ['id' => 'subscription_39lGsF0UdBfpjQ8UXdYvkX'],
                '{
                    "subscriptions": [
                        {
                            "id": "subscription_39lGsF0UdBfpjQ8UXdYvkX",
                            "broker_subscription_id": "ec407087-65f0-410d-88d7-911178120887",
                            "subscriber": {
                                "email": "benjamin.freoua@getalma.eu",
                                "phone_number": "0613595410",
                                "first_name": "Freoua",
                                "last_name": "Benjamin",
                                "address_line_1": "13 boulevard de Picpus",
                                "address_line_2": "",
                                "zip_code": "75012",
                                "city": "Paris",
                                "country": "France"
                            },
                            "contract_id": "insurance_contract_4D6UBXtagTd5DZlTGPpKuT",
                            "amount": 35000,
                            "state": "started",
                            "cms_reference": "1-1"
                        }
                    ]
                }'
            ],
            'Test with 2 subscription ids' => [
                [
                    'id' => 'subscription_39lGsF0UdBfpjQ8UXdYvkX',
                    'id' => 'subscription_7I02iVfu8vmvDMxIlinXk1'
                ],
                '{
                    "subscriptions": [
                        {
                            "id": "subscription_39lGsF0UdBfpjQ8UXdYvkX",
                            "broker_subscription_id": "ec407087-65f0-410d-88d7-911178120887",
                            "subscriber": {
                                "email": "benjamin.freoua@getalma.eu",
                                "phone_number": "0613595410",
                                "first_name": "Freoua",
                                "last_name": "Benjamin",
                                "address_line_1": "13 boulevard de Picpus",
                                "address_line_2": "",
                                "zip_code": "75012",
                                "city": "Paris",
                                "country": "France"
                            },
                            "contract_id": "insurance_contract_4D6UBXtagTd5DZlTGPpKuT",
                            "amount": 35000,
                            "state": "started",
                            "cms_reference": "1-1"
                        },
                        {
                            "id": "subscription_7I02iVfu8vmvDMxIlinXk1",
                            "broker_subscription_id": "db774ddd-f50c-4e65-b5bc-5f073acef987",
                            "subscriber": {
                                "email": "benjamin.freoua@getalma.eu",
                                "phone_number": "0613595410",
                                "first_name": "Freoua",
                                "last_name": "Benjamin",
                                "address_line_1": "13 boulevard de Picpus",
                                "address_line_2": "",
                                "zip_code": "75012",
                                "city": "Paris",
                                "country": "France"
                            },
                            "contract_id": "insurance_contract_4D6UBXtagTd5DZlTGPpKuT",
                            "amount": 35000,
                            "state": "started",
                            "cms_reference": "1-1"
                        }
                    ]
                }'
            ]
        ];
    }

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
        $this->insuranceMock->insuranceValidator = $this->insuranceValidatorMock;

        $this->arrayUtilsMock = Mockery::mock(ArrayUtils::class);
    }

    protected function tearDown()
    {
        $this->clientContext = null;
        $this->responseMock = null;
        $this->requestObject = null;
        $this->insuranceMock = null;
        $this->insuranceValidatorMock = null;
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
        $this->insuranceMock->shouldReceive('request')
            ->with(self::INSURANCE_CONTRACTS_PATH . $insuranceContractExternalId)
            ->once()
            ->andReturn($this->requestObject);
        $this->insuranceMock->shouldReceive('checkParameters')
            ->once()
            ->with($cmsReference, $insuranceContractExternalId, $productPrice);

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
            ->with(self::INSURANCE_CONTRACTS_PATH . $insuranceContractExternalId)
            ->once()
            ->andReturn($requestObject);

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
        $this->requestObject->shouldReceive('setQueryParams')
            ->once()
            ->andReturn($this->requestObject);
        $this->insuranceMock->shouldReceive('request')
            ->with(self::INSURANCE_CONTRACTS_PATH . $insuranceContractExternalId)
            ->once()
            ->andReturn($this->requestObject);
        $this->insuranceMock->shouldReceive('checkParameters')
            ->once()
            ->with($cmsReference, $insuranceContractExternalId, $productPrice);

        $this->assertEquals(
            $contractExpected,
            $this->insuranceMock->getInsuranceContract($insuranceContractExternalId, $cmsReference, $productPrice)
        );
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
        $insurance->subscription($nonArrayParam, 'orderID', $nonStringPaymentId);
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
            ->with(self::INSURANCE_SUBSCRIPTIONS_PATH)
            ->once()
            ->andReturn($requestObject);
        $insurance->setClientContext($this->clientContext);
        $this->expectException(RequestException::class);
        $insurance->subscription($subscriptionArray, 'orderId');
    }

    /**
     * @dataProvider subscriptionDataProvider
     * @param $subscriptionArray
     * @param $paymentId
     * @param $expectedSubscriptionPayload
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testSubscriptionGetRequestCall($subscriptionArray, $paymentId, $expectedSubscriptionPayload)
    {
        $expectedSubscriptionPayload['order_id'] = 'myOrderId';

        if ($paymentId) {
            $expectedSubscriptionPayload['payment_id'] = $paymentId;
        }
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('setRequestBody')->once()
            ->with($expectedSubscriptionPayload)
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);

        $insurance = Mockery::mock(Insurance::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $insurance->shouldReceive('request')
            ->with(self::INSURANCE_SUBSCRIPTIONS_PATH)
            ->once()
            ->andReturn($this->requestObject);
        $insurance->setClientContext($this->clientContext);
        $insurance->subscription($subscriptionArray, 'myOrderId', $paymentId);
    }

    /**
     * @return void
     */
    public function testSubscriptionMethodExist()
    {
        $insurance = new Insurance($this->clientContext);
        $this->assertTrue(method_exists($insurance, 'getSubscription'));
    }

    /**
     * @dataProvider getSubscriptionsRightParamDataProvider
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testGetSubscriptionRequestIsCalled($subscriptionIds)
    {
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('setQueryParams')->once()->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('get')->once()->andReturn($this->responseMock);
        $this->insuranceValidatorMock->shouldReceive('checkSubscriptionIds')->once();
        $this->insuranceMock->shouldReceive('request')
            ->with(self::INSURANCE_SUBSCRIPTIONS_PATH)
            ->once()
            ->andReturn($this->requestObject);

        $this->insuranceMock->getSubscription($subscriptionIds);
    }

    /**
     * @dataProvider getSubscriptionsRightParamDataProvider
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testGetSubscriptionThrowExceptionIfResponseHasAnError($subscriptionIds)
    {
        $this->responseMock->shouldReceive('isError')->once()->andReturn(true);
        $this->requestObject->shouldReceive('setQueryParams')->once()->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('get')->once()->andReturn($this->responseMock);
        $this->insuranceMock->shouldReceive('request')
            ->with(self::INSURANCE_SUBSCRIPTIONS_PATH)
            ->once()
            ->andReturn($this->requestObject);

        $this->insuranceValidatorMock->shouldReceive('checkSubscriptionIds')
            ->once()
            ->with($subscriptionIds);

        $this->expectException(RequestException::class);
        $this->insuranceMock->getSubscription($subscriptionIds);
    }

    /**
     * @dataProvider nonArrayParamGetSubscriptionDataProvider
     * @param $subscriptionIds
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testGetSubscriptionThrowExceptionIfWeDontSendAnArray($subscriptionIds)
    {
        $this->insuranceValidatorMock->shouldReceive('checkSubscriptionIds')
            ->once()
            ->with($subscriptionIds)
            ->andThrow(ParametersException::class);
        $this->expectException(ParametersException::class);
        $this->insuranceMock->getSubscription($subscriptionIds);
    }

    /**
     * @dataProvider getSubscriptionsRightParamDataProvider
     * @param $subscriptionIds
     * @param $json
     * @return void
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testGetSubscriptionsReturnApiResponse($subscriptionIds, $json)
    {
        $this->responseMock->json = $json;
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('get')->once()->andReturn($this->responseMock);
        $this->requestObject->shouldReceive('setQueryParams')->once()->andReturn($this->requestObject);
        $this->insuranceMock->shouldReceive('request')
            ->with(self::INSURANCE_SUBSCRIPTIONS_PATH)
            ->once()
            ->andReturn($this->requestObject);
        $this->insuranceValidatorMock->shouldReceive('checkSubscriptionIds')->once();

        $this->assertEquals($json, $this->insuranceMock->getSubscription($subscriptionIds));
    }

    /**
     * @return void
     * @throws RequestError
     */
    public function testGivenInvalidCmsReferenceArrayNoCallEndpointAndReturnFalse()
    {
        $this->insuranceValidatorMock->shouldReceive('checkCmsReference')
            ->once()
            ->andThrow(ParametersException::class);
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error')->once();
        $this->clientContext->logger = $loggerMock;
        $this->insuranceMock->setClientContext($this->clientContext);
        $this->assertNull($this->insuranceMock->sendCustomerCart(['123','456'], 42));
    }

    /**
     * @return void
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testCancelSubscriptionCallRequestWithSubscriptionArrayPayloadAndNoThrowExceptionForResponse200()
    {
        $subscriptionCancelPayload = ' subscriptionId1 ';
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);
        $this->insuranceMock->shouldReceive('request')
            ->with(self::INSURANCE_SUBSCRIPTIONS_PATH . '/subscriptionId1/void')
            ->once()
            ->andReturn($this->requestObject);
        $this->insuranceMock->cancelSubscription($subscriptionCancelPayload);
    }

    /**
     * @return void
     * @throws RequestError
     */
    public function testSendCustomerCartCallApiPostCustomerCartWithCmsReferencesArray()
    {
        $cartId = 42;
        $this->insuranceValidatorMock->shouldReceive('checkCmsReference')
            ->once();
        $this->requestObject->shouldReceive('setRequestBody')->once()->with(
            [
                'cms_references' => ['123','456']
            ]
        )->andReturn($this->requestObject);
        $this->insuranceMock->shouldReceive('request')
            ->with(self::INSURANCE_CUSTOMER_CART_PATH)
            ->once()
            ->andReturn($this->requestObject);
        $this->insuranceMock->shouldReceive('addCustomerSessionToRequest')->once()->with(
            $this->requestObject,
            null,
            $cartId
        );
        $this->requestObject->shouldReceive('post')->once();
        $this->assertNull($this->insuranceMock->sendCustomerCart(['123','456'], $cartId));
    }

    /**
     * @return void
     * @throws ParametersException
     * @throws RequestError
     * @throws RequestException
     */
    public function testCancelSubscriptionCallRequestWithSubscriptionArrayPayloadAndThrowExceptionForResponseUpperThan400()
    {
        $this->expectException(RequestException::class);
        $subscriptionCancelPayload = 'subscriptionId1';
        $this->responseMock->shouldReceive('isError')->once()->andReturn(true);
        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);
        $this->insuranceMock->shouldReceive('request')
            ->with(self::INSURANCE_SUBSCRIPTIONS_PATH . '/subscriptionId1/void')
            ->once()
            ->andReturn($this->requestObject);
        $this->insuranceMock->cancelSubscription($subscriptionCancelPayload);
    }

    /**
     * @dataProvider cancelSubscriptionErrorPayloadDataProvider
     * @param $payload
     * @return void
     * @throws ParametersException
     */
    public function testCheckSubscriptionIdFormatThrowParamsErrorForBadPayload($payload)
    {
        $this->expectException(ParametersException::class);
        $this->insuranceMock->checkSubscriptionIdFormat($payload);
    }

    public function cancelSubscriptionErrorPayloadDataProvider()
    {
        return [
            'Null payload' => [
                'subscriptionIdsArray' => null
            ],
            'empty string payload' => [
                'subscriptionIdsArray' => ''
            ],
            'empty array payload' => [
                'subscriptionIdsArray' => []
            ],
            'Subscription Object payload' => [
                'subscriptionIdsArray' => $this->createMock(Subscription::class)
            ],
        ];
    }
}
