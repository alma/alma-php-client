<?php

namespace Alma\API\Tests\Unit\Entity;

use Alma\API\Domain\Entity\EligibilityList;
use Alma\API\Entity\Eligibility;
use PHPUnit\Framework\TestCase;

class EligibilityListTest extends TestCase
{
    public function testEligibilityList() {
        $eligibilityList = new EligibilityList();

        // Pay now plans
        $eligibilityList->add($this->eligibilityFactory(1, 0, 0));
        // Pay-later plans
        $eligibilityList->add($this->eligibilityFactory(1, 2, 0));
        $eligibilityList->add($this->eligibilityFactory(1, 0, 15));
        // Pnx plans
        $eligibilityList->add($this->eligibilityFactory(2, 0, 0));
        $eligibilityList->add($this->eligibilityFactory(3, 0, 0));
        $eligibilityList->add($this->eligibilityFactory(4, 0, 0));
        // Credit plans
        $eligibilityList->add($this->eligibilityFactory(10, 0, 0));
        $eligibilityList->add($this->eligibilityFactory(12, 0, 0));

        $this->assertInstanceOf(Eligibility::class, $eligibilityList->getByPlanKey('general_2_0_0'));
        $this->assertCount(3, $eligibilityList->filterEligibilityList('pnx'));
        $this->assertCount(2, $eligibilityList->filterEligibilityList('credit'));
        $this->assertCount(2, $eligibilityList->filterEligibilityList('pay-later'));
        $this->assertCount(1, $eligibilityList->filterEligibilityList('pay-now'));
        $this->assertCount(0, $eligibilityList->filterEligibilityList('never'));
    }

    private function eligibilityFactory(int $installments, int $deferredDays, int $deferredMonths ): Eligibility
    {
        return new Eligibility([
            'eligible' => true,
            'deferred_days' => $deferredDays,
            'deferred_months' => $deferredMonths,
            'installments_count' => $installments,
            'customer_fee' => 0 ,
            'customer_total_cost_amount' => 0,
            'customer_total_cost_bps' => 0,
            'payment_plan' => [],
            'annual_interest_rate' => 0,
        ]);
    }

}
