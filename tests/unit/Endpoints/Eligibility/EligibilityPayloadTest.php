<?php

namespace Alma\API\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;

use Alma\API\Endpoints\Payments;
use Alma\API\Lib\ClientOptionsValidator;
use Alma\API\ClientContext;
use Alma\API\Request;
use Alma\API\ParamsError;
use Alma\API\RequestError;
use Alma\API\Endpoints\Payments\Eligibility\EligibilityPayload;

/**
 * Class EligibilityPayloadTest
 */
class EligibilityPayloadTest extends TestCase
{

    /**
     * DATA PROVIDERS
     */

    /**
     * Return input to test EligibilityPayload
     * @return array[]
     */
    public function getEligibilityPayload() {
        return [
            [[
                "purchase_amount" => 13890,
                "queries" => [
                    [
                        "allowed"=>true,
                        "deferred_days"=>30,
                        "deferred_trigger_limit_days"=>null,
                        "installments_count"=>1,
                        "max_purchase_amount"=>200000,
                        "min_purchase_amount"=>5000
                    ],
                    [
                        "allowed"=>true,
                        "deferred_days"=>15,
                        "deferred_trigger_limit_days"=>null,
                        "installments_count"=>1,
                        "max_purchase_amount"=>200000,
                        "min_purchase_amount"=>25000
                    ],
                    [
                        "allowed"=>true,
                        "deferred_days"=>0,
                        "deferred_trigger_limit_days"=>null,
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
                        "deferred_trigger_limit_days"=>null,
                        "installments_count"=>4,
                        "max_purchase_amount"=>200000,
                        "min_purchase_amount"=>60000
                    ]
                ]
            ]],
        ];
    }

    /**
     * TESTS
     */

    /**
     * Test the partialRefund method with valid datas
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


    public function tearDown()
    {
    }
}
