<?php

namespace Alma\API\Tests\Unit\Entity;

use Alma\API\Entity\Eligibility;
use Alma\API\Entity\EligibilityList;
use PHPUnit\Framework\TestCase;

class EligibilityListTest extends TestCase
{
    public function testEligibilityList() {
        $eligibilityList = new EligibilityList();

        // Pay now plans
        $eligibilityList->add(new Eligibility([
            'is_eligible' => true,
            'installments_count' => 1,
            'deferred_months' => 0,
            'deferred_days' => 0,
        ]));
        // Pay-later plans
        $eligibilityList->add(new Eligibility([
            'is_eligible' => true,
            'installments_count' => 1,
            'deferred_months' => 2,
            'deferred_days' => 0,
        ]));
        $eligibilityList->add(new Eligibility([
            'is_eligible' => true,
            'installments_count' => 1,
            'deferred_months' => 0,
            'deferred_days' => 15,
        ]));
        // Pnx plans
        $eligibilityList->add(new Eligibility([
            'is_eligible' => true,
            'installments_count' => 2,
            'deferred_months' => 0,
            'deferred_days' => 0,
        ]));
        $eligibilityList->add(new Eligibility([
            'is_eligible' => true,
            'installments_count' => 3,
            'deferred_months' => 0,
            'deferred_days' => 0,
        ]));
        $eligibilityList->add(new Eligibility([
            'is_eligible' => true,
            'installments_count' => 4,
            'deferred_months' => 0,
            'deferred_days' => 0,
        ]));
        // Credit plans
        $eligibilityList->add(new Eligibility([
            'is_eligible' => true,
            'installments_count' => 10,
            'deferred_months' => 0,
            'deferred_days' => 0,
        ]));
        $eligibilityList->add(new Eligibility([
            'is_eligible' => true,
            'installments_count' => 12,
            'deferred_months' => 0,
            'deferred_days' => 0,
        ]));

        $this->assertInstanceOf(Eligibility::class, $eligibilityList->getByPlanKey('general_2_0_0'));
        $this->assertCount(3, $eligibilityList->filterEligibilityList('pnx'));
        $this->assertCount(2, $eligibilityList->filterEligibilityList('credit'));
        $this->assertCount(2, $eligibilityList->filterEligibilityList('pay-later'));
        $this->assertCount(1, $eligibilityList->filterEligibilityList('pay-now'));
        $this->assertCount(0, $eligibilityList->filterEligibilityList('never'));
    }

}
