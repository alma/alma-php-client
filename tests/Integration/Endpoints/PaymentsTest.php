<?php

namespace Alma\API\Tests\Integration\Endpoints;

use Alma\API\Tests\Integration\TestHelpers\ClientTestHelper;
use Alma\API\Tests\Integration\TestHelpers\PaymentTestHelper;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class PaymentsTest extends MockeryTestCase
{
    protected static $payment;
    protected static $almaClient;

    public static function setUpBeforeClass(): void
    {
        PaymentsTest::$payment = PaymentTestHelper::createPayment(26500, 3);
        PaymentsTest::$almaClient = ClientTestHelper::getAlmaClient();
    }

    private function checkEligibility($amount, $eligible)
    {
        $eligibilityPayload = [
            'purchase_amount' => $amount,
            'queries' => [
                ['installments_count' => 3],
            ]
        ];
        $eligibility = PaymentsTest::$almaClient->payments->eligibility($eligibilityPayload);
        $eligibility = $eligibility['general_3_0_0'];

        $this->assertEquals($eligible, $eligibility->isEligible);

        if (!$eligible) {
            $this->assertArrayHasKey('purchase_amount', $eligibility->reasons);
            $this->assertEquals('invalid_value', $eligibility->reasons['purchase_amount']);

            $this->assertArrayHasKey('purchase_amount', $eligibility->constraints);
            $this->assertArrayHasKey('minimum', $eligibility->constraints['purchase_amount']);
            $this->assertArrayHasKey('maximum', $eligibility->constraints['purchase_amount']);
        }
    }

    public function testCanCheckEligibility()
    {
        $this->checkEligibility(1, false);
        $this->checkEligibility(20000, true);
        $this->checkEligibility(500000, false);
    }

    public function testCanCreateAPayment()
    {
        $payment = PaymentsTest::$payment;
        $this->assertEquals(26500, $payment->purchase_amount);
    }

    public function testCanFetchAPayment()
    {
        $p1 = PaymentsTest::$payment;
        $p2 = PaymentsTest::$almaClient->payments->fetch($p1->id);

        $this->assertEquals($p1->id, $p2->id);
    }
}
