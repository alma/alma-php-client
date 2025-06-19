<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Endpoint\EligibilityEndpoint;
use Alma\API\Endpoint\ShareOfCheckoutEndpoint;
use Alma\API\Exceptions\ClientException;
use Alma\API\Exceptions\EligibilityServiceException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;
use Psr\Log\NullLogger;

/**
 * Class Payments
 */
class EligibilityEndpointTest extends AbstractEndpointSetUp
{
    const MERCHANT_REF = "merchant_ref";

    const SERVER_REQUEST_ELIGIBILITY_RESPONSE_JSON = '
    [
        {
            "customer_total_cost_amount": 0,
            "customer_total_cost_bps": 0,
            "deferred_days": 0,
            "deferred_months": 0,
            "eligible": true,
            "installments_count": 3,
            "payment_plan": [
                {
                    "customer_fee": 0,
                    "customer_interest": 0,
                    "due_date": 1628067070,
                    "purchase_amount": 20000,
                    "total_amount": 20000
                },
                {
                    "customer_fee": 0,
                    "customer_interest": 0,
                    "due_date": 1630745470,
                    "purchase_amount": 20000,
                    "total_amount": 20000
                },
                {
                    "customer_fee": 0,
                    "customer_interest": 0,
                    "due_date": 1633337470,
                    "purchase_amount": 20000,
                    "total_amount": 20000
                }
            ],
            "purchase_amount": 60000
        }
    ]';

    const SERVER_REQUEST_NO_ELIGIBILITY_RESPONSE_JSON = '
    [
        {
            "purchase_amount": 130,
            "installments_count": 3,
            "deferred_days": 0,
            "deferred_months": 0,
            "constraints": {
                "purchase_amount": {
                    "minimum": 5000,
                    "maximum": 200000
                }
            },
            "reasons": {
                "purchase_amount": "invalid_value"
            },
            "eligible": false
        }
    ]';

    /**
     * Return input to test eligibility
     * @return array[]
     */
    public static function eligibilityProvider(): array
    {
        return [
            [[
                'params' => [
                    'purchase_amount' => 15000,
                    'queries'         => [
                        [
                            'deferred_days'      => 0,
                            'deferred_months'    => 0,
                            'deferred_trigger'   => false,
                            'installments_count' => 3,
                        ],
                    ],
                ],
                'response_code' => 200,
                'is_error' => false,
                'eligibility' => true
            ]],
            [[
                'params' => [
                    'purchase_amount' => 1500,
                    'queries'         => [
                        [
                            'deferred_days'      => 0,
                            'deferred_months'    => 0,
                            'deferred_trigger'   => false,
                            'installments_count' => 3,
                        ],
                    ],
                ],
                'response_code' => 200,
                'is_error' => false,
                'eligibility' => false
            ]],
            [[
                'params' => [
                    'purchase_amount' => 15000,
                    'queries'         => [
                        [
                            'deferred_days'      => 0,
                            'deferred_months'    => 0,
                            'deferred_trigger'   => false,
                            'installments_count' => 3,
                        ],
                        [
                            'deferred_days'      => 0,
                            'deferred_months'    => 0,
                            'deferred_trigger'   => false,
                            'installments_count' => 4,
                        ],
                    ],
                ],
                'response_code' => 200,
                'is_error' => false,
                'eligibility' => false
            ]],
        ];
    }

    /**
     * @dataProvider eligibilityProvider
     * @param array $data
     * @return void
     * @throws EligibilityServiceException
     */
    public function testEligibility(array $data)
    {
        // Mocks
        $jsonResponse = self::SERVER_REQUEST_ELIGIBILITY_RESPONSE_JSON;
        if ($data['eligibility'] !== true) {
            $jsonResponse = self::SERVER_REQUEST_NO_ELIGIBILITY_RESPONSE_JSON;
        }
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('getStatusCode')->andReturn($data['response_code']);
        $responseMock->shouldReceive('isError')->andReturn($data['is_error']);
        $responseMock->shouldReceive('getReasonPhrase')->andReturn('my_error');
        $responseMock->shouldReceive('getBody')->andReturn($jsonResponse);
        $responseMock->shouldReceive('getJson')->andReturn(json_decode($jsonResponse, true));
        $this->clientMock->shouldReceive('sendRequest')->andReturn($responseMock);

        // EligibilityService
        $eligibilityServiceMock = Mockery::mock(EligibilityEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $eligibilityServiceMock->shouldReceive('createPostRequest')
            ->with(EligibilityEndpoint::ELIGIBILITY_ENDPOINT, $data['params'])
            ->once();

        // Call
        $response = $eligibilityServiceMock->getEligibilityList($data['params']);
        foreach ($response as $eligibility) {
            // Assertions
            $this->assertEquals($data['eligibility'], $eligibility->isEligible());
        }
    }

    /**
     * Ensure we can catch EligibilityServiceException
     * @return void
     * @throws EligibilityServiceException
     */
    public function testEligibilityServiceException()
    {
        // Mocks
        $badResponseMock = Mockery::mock(Response::class);
        $badResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $badResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $badResponseMock->shouldReceive('isError')->andReturn(true);
        $badResponseMock->shouldReceive('getBody')->andReturn(self::SERVER_REQUEST_NO_ELIGIBILITY_RESPONSE_JSON);
        $this->clientMock->shouldReceive('sendRequest')->andReturn($badResponseMock);

        // EligibilityService
        $eligibilityServiceMock = Mockery::mock(EligibilityEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $eligibilityServiceMock->shouldReceive('createPostRequest')
            ->with(EligibilityEndpoint::ELIGIBILITY_ENDPOINT, [])
            ->once();

        // Call
        $this->expectException(EligibilityServiceException::class);
        $eligibilityServiceMock->getEligibilityList([]);
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws EligibilityServiceException
     */
    public function testEligibilityClientExceptionInterface()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')
            ->andThrow(ClientException::class);

        // EligibilityService
        $eligibilityServiceMock = Mockery::mock(EligibilityEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $eligibilityServiceMock->shouldReceive('createPostRequest')
            ->with(EligibilityEndpoint::ELIGIBILITY_ENDPOINT, [])
            ->once();

        // Call
        $this->expectException(EligibilityServiceException::class);
        $eligibilityServiceMock->getEligibilityList([]);
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws EligibilityServiceException
     */
    public function testEligibilityRequestException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')
            ->andThrow(new RequestException('Request exception'));

        // EligibilityService
        $eligibilityServiceMock = Mockery::mock(EligibilityEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $eligibilityServiceMock->shouldReceive('createPostRequest')
            ->with(EligibilityEndpoint::ELIGIBILITY_ENDPOINT, [])
            ->once();

        // Call
        $this->expectException(EligibilityServiceException::class);
        $eligibilityServiceMock->getEligibilityList([]);
    }
}
