<?php

namespace Alma\API\Tests\Unit\Entity;

use Alma\API\Domain\Entity\Eligibility;
use Alma\API\Domain\Entity\FeePlan;
use Alma\API\Infrastructure\Exception\ParametersException;
use PHPUnit\Framework\TestCase;

class EligibilityTest extends TestCase
{
    private function getNotEligibleReponse()
    {
        return [
            'purchase_amount' => 3762300,
            'installments_count' => 10,
            'deferred_days' => 0,
            'deferred_months' => 0,
            'constraints' => [
                'purchase_amount' => [
                    'minimum' => 5000,
                    'maximum' => 200000
                ]
            ],
            'reasons' => [
                'purchase_amount' => 'invalid_value'
            ],
            'eligible' => false
        ];
    }

    private function getEligiblePaymentPlan()
    {
        return [
            [
                'due_date' => 1757063789,
                'total_amount' => 70108,
                'customer_fee' => 3440,
                'customer_interest' => 0,
                'purchase_amount' => 66668,
                'localized_due_date' => "aujourd'hui",
                'time_delta_from_start' => null
            ],
            [
                'due_date' => 1759655789,
                'total_amount' => 66666,
                'customer_fee' => 0,
                'customer_interest' => 0,
                'purchase_amount' => 66666,
                'localized_due_date' => "5 octobre 2025",
                'time_delta_from_start' => null
            ],
            [
                'due_date' => 1762334189,
                'total_amount' => 66666,
                'customer_fee' => 0,
                'customer_interest' => 0,
                'purchase_amount' => 66666,
                'localized_due_date' => "5 novembre 2025",
                'time_delta_from_start' => null
            ]
        ];
    }

    private function getEligibleResponse()
    {
        return [
            'purchase_amount' => 200000,
            'installments_count' => 3,
            'deferred_days' => 0,
            'deferred_months' => 0,
            'payment_plan' => $this->getEligiblePaymentPlan(),
            'customer_fee' => 3440,
            'customer_interest' => 0,
            'customer_total_cost_amount' => 3441,
            'customer_total_cost_bps' => 172,
            'annual_interest_rate' => 2330,
            'modulated_first_installment' => false,
            'eligible' => true
        ];
    }


    /**
     * @throws ParametersException
     */
    public function testConstructEligibilityWithEligibleResponse()
    {
        $eligibility = new Eligibility($this->getEligibleResponse());
        $this->assertSame(FeePlan::KIND_GENERAL, $eligibility->getKind());
        $this->assertTrue($eligibility->isEligible());
        $this->assertSame(0, $eligibility->getDeferredDays());
        $this->assertSame(0, $eligibility->getDeferredMonths());
        $this->assertSame(3, $eligibility->getInstallmentsCount());
        $this->assertSame(3441, $eligibility->getCustomerTotalCostAmount());
        $this->assertSame(172, $eligibility->getCustomerTotalCostBps());
        $this->assertSame(3440, $eligibility->getCustomerFee());
        $this->assertSame(2330, $eligibility->getAnnualInterestRate());
        $this->assertSame($this->getEligiblePaymentPlan(), $eligibility->getPaymentPlan());
        $this->assertEmpty($eligibility->getConstraints());
        $this->assertEmpty($eligibility->getReasons());
        $this->assertSame('general_3_0_0', $eligibility->getPlanKey());

    }

    public function testConstructEligibilityWithNotEligibleResponse()
    {
        $eligibility = new Eligibility($this->getNotEligibleReponse());
        $this->assertFalse($eligibility->isEligible());
        $this->assertSame(0, $eligibility->getDeferredDays());
        $this->assertSame(0, $eligibility->getDeferredMonths());
        $this->assertSame(10, $eligibility->getInstallmentsCount());
        $this->assertEmpty($eligibility->getCustomerTotalCostAmount());
        $this->assertEmpty($eligibility->getCustomerTotalCostBps());
        $this->assertEmpty($eligibility->getCustomerFee());
        $this->assertEmpty($eligibility->getAnnualInterestRate());
        $this->assertEmpty($eligibility->getPaymentPlan());
        $this->assertSame([
            'purchase_amount' => [
                'minimum' => 5000,
                'maximum' => 200000
            ]
        ], $eligibility->getConstraints());
        $this->assertSame(['purchase_amount' => 'invalid_value'], $eligibility->getReasons());
        $this->assertSame('general_10_0_0', $eligibility->getPlanKey());
    }

}
