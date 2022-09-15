<?php

namespace Alma\API\Tests\Unit;

use PHPUnit\Framework\TestCase;

use Alma\API\ParamsError;
use Alma\API\Services\Eligibility\EligibilityPayload;

/**
 * Class EligibilityPayloadTest
 */
class EligibilityPayloadTest extends TestCase
{

    /**
     * DATA PROVIDERS
     */

    /**
     * Return input to test getEligibilityPayload
     * @return array[]
     */
    public function getEligibilityPayload() {
        return [
            'normal payload' => [[
                "purchase_amount" => 13890,
                "queries" => [
                    [
                        "allowed"=>true,
                        "deferred_days"=>30,
                        "installments_count"=>1,
                        "max_purchase_amount"=>200000,
                        "min_purchase_amount"=>5000
                    ],
                    [
                        "allowed"=>true,
                        "deferred_days"=>15,
                        "installments_count"=>1,
                        "max_purchase_amount"=>200000,
                        "min_purchase_amount"=>25000
                    ],
                    [
                        "allowed"=>true,
                        "deferred_days"=>0,
                        "installments_count"=>2,
                        "max_purchase_amount"=>200000,
                        "min_purchase_amount"=>5000
                    ],
                    [
                        "allowed"=>true,
                        "deferred_days"=>0,
                        "deferred_trigger_limit_days"=>30,
                        "installments_count"=>3,
                        "max_purchase_amount"=>50000,
                        "min_purchase_amount"=>10000
                    ],
                    [
                        "allowed"=>true,
                        "deferred_days"=>0,
                        "installments_count"=>4,
                        "max_purchase_amount"=>200000,
                        "min_purchase_amount"=>60000
                    ]
                ]
            ]],
            'missing fields but valid' => [[
                "purchase_amount" => 15000,
                "queries" => [
                    [
                        "installments_count"=> 3,
                    ],
                    [
                        "installments_count"=> 1,
                        "deferred_days"=> 15,
                    ]
                ]
            ]]
        ];
    }

    /**
     * Return input to test getBadEligibilityPayload
     * @return array[]
     */
    public function getBadEligibilityPayload() {
        return [
            'no purchase_amount' => [[
                "queries" => [
                    [
                        "allowed"=>true,
                        "deferred_days"=>30,
                        "installments_count"=>1,
                        "max_purchase_amount"=>200000,
                        "min_purchase_amount"=>5000
                    ],
                ]
                ], ParamsError::class],
            'empty queries' => [[
                "purchase_amount" => 15000,
                "queries" => []
            ], ParamsError::class],
            'no queries' => [[
                "purchase_amount" => 15000,
            ], ParamsError::class],
            'unknown param' => [[
                "purchase_amount" => 15000,
                "queries" => [
                    ["installments_count"=> 3,],
                ],
                "the_spanish_inquisition" => 15000,
            ], ParamsError::class],
        ];
    }

    /**
     * TESTS
     */

    /**
     * Test the eligibility payload to be send to the API with valid data
     * @dataProvider getEligibilityPayload
     * @return void
     */
    public function testEligibilityPayload($data)
    {
        $eligibilityPayload = new EligibilityPayload($data);
        $this->assertEquals(
            $data,
            $eligibilityPayload->toPayload()
        );
    }

    /**
     * Test the eligibility payload to be send to the API with invalid data
     * @dataProvider getBadEligibilityPayload
     * @return void
     */
    public function testBadEligibilityPayload($data, $expectedException)
    {
        $this->expectException($expectedException);

        $eligibilityPayload = new EligibilityPayload($data);
        $eligibilityPayload->validate();
    }
}
