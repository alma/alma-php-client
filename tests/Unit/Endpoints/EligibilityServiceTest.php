<?php

namespace Alma\API\Tests\Unit\Endpoints;

use Alma\API\Endpoints\EligibilityService;
use Alma\API\Exceptions\EligibilityServiceException;
use Alma\API\Response;
use Mockery;

/**
 * Class Payments
 */
class EligibilityServiceTest extends AbstractEndpointServiceTest
{
    const MERCHANT_REF = "merchant_ref";

    const SERVER_REQUEST_ELIGIBILITY_RESPONSE_JSON = '{
        "purchase_amount": 15000,
        "installments_count": 3,
        "deferred_days": 0,
        "deferred_months": 0,
        "payment_plan": [
            {
                "due_date": 1745857122,
                "total_amount": 5180,
                "customer_fee": 180,
                "customer_interest": 0,
                "purchase_amount": 5000,
                "localized_due_date": "aujourd\'hui",
                "time_delta_from_start": null
            },
            {
                "due_date": 1748449122,
                "total_amount": 5000,
                "customer_fee": 0,
                "customer_interest": 0,
                "purchase_amount": 5000,
                "localized_due_date": "28 mai 2025",
                "time_delta_from_start": null
            },
            {
                "due_date": 1751127522,
                "total_amount": 5000,
                "customer_fee": 0,
                "customer_interest": 0,
                "purchase_amount": 5000,
                "localized_due_date": "28 juin 2025",
                "time_delta_from_start": null
            }
        ],
        "customer_fee": 180,
        "customer_interest": 0,
        "customer_total_cost_amount": 180,
        "customer_total_cost_bps": 120,
        "annual_interest_rate": 1566,
        "modulated_first_installment": false,
        "eligible": true
    }';

    const SERVER_REQUEST_NO_ELIGIBILITY_RESPONSE_JSON = '{
        "purchase_amount": 1500,
        "installments_count": 3,
        "deferred_days": 0,
        "deferred_months": 0,
        "constraints": {
            "purchase_amount": {
                "minimum": 5000,
                "maximum": 300000
            }
        },
        "reasons": {
            "purchase_amount": "invalid_value"
        },
        "eligible": false
    }';

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
        $responseMock = Mockery::mock(Response::class, [200, [], $jsonResponse])
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
        if (is_array($response)) {
            $response = $response[0];
        }

        // Assertions
        $this->assertEquals($data['eligibility'], $response->isEligible());
    }
}
