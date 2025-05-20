<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Endpoint\EligibilityService;
use Alma\API\Exceptions\ClientException;
use Alma\API\Exceptions\EligibilityServiceException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Response;
use Mockery;

/**
 * Class Payments
 */
class EligibilityServiceTest extends AbstractEndpointService
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
                'eligibility' => false
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
                'response_code' => 503,
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
        $responseMock = Mockery::mock(Response::class, [$data['response_code'], [], $jsonResponse])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $responseMock->shouldReceive('isError')->andReturn(false);
        $this->clientMock->shouldReceive('sendRequest')->andReturn($responseMock);

        // EligibilityService
        $paymentServiceMock = Mockery::mock(EligibilityService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')
            ->with(EligibilityService::ELIGIBILITY_ENDPOINT, $data['params'])
            ->once();

        // Call
        $response = $paymentServiceMock->eligibility($data['params']);
        foreach ($response as $eligibility) {
            // Assertions
            $this->assertEquals($data['eligibility'], $eligibility->isEligible());
        }
    }

    public function testEligibilityServiceException()
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class, [503])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $responseMock->shouldReceive('isError')->andReturn(true);
        $this->clientMock->shouldReceive('sendRequest')->andReturn($responseMock);

        // EligibilityService
        $paymentServiceMock = Mockery::mock(EligibilityService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')
            ->with(EligibilityService::ELIGIBILITY_ENDPOINT, [])
            ->once();

        // Call
        $this->expectException(EligibilityServiceException::class);
        $paymentServiceMock->eligibility([]);
    }

    public function testEligibilityClientExceptionInterface()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')
            ->andThrow(new ClientException('Client exception'));

        // EligibilityService
        $paymentServiceMock = Mockery::mock(EligibilityService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')
            ->with(EligibilityService::ELIGIBILITY_ENDPOINT, [])
            ->once();

        // Call
        $this->expectException(EligibilityServiceException::class);
        $paymentServiceMock->eligibility([]);
    }

    public function testEligibilityRequestException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')
            ->andThrow(new RequestException('Request exception'));

        // EligibilityService
        $paymentServiceMock = Mockery::mock(EligibilityService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')
            ->with(EligibilityService::ELIGIBILITY_ENDPOINT, [])
            ->once();

        // Call
        $this->expectException(EligibilityServiceException::class);
        $paymentServiceMock->eligibility([]);
    }
}
