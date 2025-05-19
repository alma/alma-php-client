<?php

namespace Alma\API\Tests\Endpoint\Result;

use Alma\API\Endpoint\Result\Eligibility;
use PHPUnit\Framework\TestCase;

class EligibilityTest extends TestCase
{
    public static function eligibilityConstructorHandlesScenariosProvider(): array
    {
        return [
            'eligible_with_reasons_and_constraints' => [
                ['eligible' => true, 'reasons' => ['reason1'], 'constraints' => ['constraint1']],
                200,
                true,
                ['reason1'],
                ['constraint1']
            ],
            'eligible_with_payment_plan' => [
                ['eligible' => true, 'payment_plan' => ['plan1'], 'installments_count' => 3],
                200,
                true,
                [],
                [],
                ['plan1'],
                3
            ]
        ];
    }

    /**
     * Ensure Eligibility constructor handles various scenarios
     * @dataProvider eligibilityConstructorHandlesScenariosProvider
     * @param array $data
     * @param int|null $responseCode
     * @param bool $expectedIsEligible
     * @param array $expectedReasons
     * @param array $expectedConstraints
     * @param array|null $expectedPaymentPlan
     * @param int|null $expectedInstallmentsCount
     * @return void
     */
    public function testEligibilityConstructorHandlesScenarios(
        array $data,
        ?int $responseCode,
        bool $expectedIsEligible,
        array $expectedReasons,
        array $expectedConstraints,
        ?array $expectedPaymentPlan = null,
        ?int $expectedInstallmentsCount = null
    ) {
        $eligibility = new Eligibility($data, $responseCode);

        $this->assertSame($expectedIsEligible, $eligibility->isEligible());
        $this->assertSame($expectedReasons, $eligibility->getReasons());
        $this->assertSame($expectedConstraints, $eligibility->getConstraints());

        if ($expectedPaymentPlan !== null) {
            $this->assertSame($expectedPaymentPlan, $eligibility->getPaymentPlan());
        }

        if ($expectedInstallmentsCount !== null) {
            $this->assertSame($expectedInstallmentsCount, $eligibility->getInstallmentsCount());
        }
    }

    public static function annualInterestRateHandlesScenariosProvider(): array
    {
        return [
            'annual_interest_rate_set' => [
                ['annual_interest_rate' => 500],
                500
            ],
            'annual_interest_rate_not_set' => [
                [],
                null
            ],
        ];
    }

    /**
     * Ensure annualInterestRate is handled correctly
     * @dataProvider annualInterestRateHandlesScenariosProvider
     * @param array $data
     * @param int|null $expectedAnnualInterestRate
     * @return void
     */
    public function testAnnualInterestRateHandlesScenarios(array $data, ?int $expectedAnnualInterestRate)
    {
        $eligibility = new Eligibility($data);

        $this->assertSame($expectedAnnualInterestRate, $eligibility->getAnnualInterestRate());
    }

    public static function eligibilityProvider(): array
    {
        return [
            'variable_set' => [
                ['customer_total_cost_amount' => 500, 'customer_total_cost_bps' => 500],
                ['customer_total_cost_bps' => 500, 'customer_total_cost_amount' => 500]
            ],
            'variable_not_set' => [
                [],
                ['customer_total_cost_bps' => 0, 'customer_total_cost_amount' => 0]
            ],
        ];
    }

    /**
     * Ensure annualInterestRate is handled correctly
     * @dataProvider eligibilityProvider
     * @param array $data
     * @param array $expectedValues
     * @return void
     */
    public function testEligibilityScenarios(array $data, array $expectedValues)
    {
        $eligibility = new Eligibility($data);

        $this->assertSame($expectedValues['customer_total_cost_amount'], $eligibility->getCustomerTotalCostAmount());
        $this->assertSame($expectedValues['customer_total_cost_bps'], $eligibility->getCustomerTotalCostBps());
    }
}
