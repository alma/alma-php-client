<?php

namespace Alma\API\Tests\Unit\Entity;

use Alma\API\Entity\Eligibility;
use Alma\API\Entity\FeePlan;
use Alma\API\Entity\FeePlanList;
use PHPUnit\Framework\TestCase;

class FeePlanListTest extends TestCase
{
    public function testFeePlanList() {
        $feePlanList = new FeePlanList();

        $feePlan = (new FeePlan([
            'installments_count'  => 0,
            'deferred_months'     => 1,
            'deferred_days'       => 0,
            'min_purchase_amount' => 500,
            'max_purchase_amount' => 1000,
        ]));

        // Pay now plans
        $feePlanList->add(new FeePlan([
            'installments_count'  => 1,
            'deferred_months'     => 0,
            'deferred_days'       => 0,
            'min_purchase_amount' => 500,
            'max_purchase_amount' => 1000,
        ]));
        // Pay-later plans
        $feePlanList->add(new FeePlan([
            'installments_count'  => 1,
            'deferred_months'     => 2,
            'deferred_days'       => 0,
            'min_purchase_amount' => 500,
            'max_purchase_amount' => 1000,
        ]));
        $feePlanList->add(new FeePlan([
            'installments_count'  => 1,
            'deferred_months'     => 0,
            'deferred_days'       => 15,
            'min_purchase_amount' => 500,
            'max_purchase_amount' => 1000,
        ]));
        // Pnx plans
        $feePlanList->add(new FeePlan([
            'installments_count'  => 2,
            'deferred_months'     => 0,
            'deferred_days'       => 0,
            'min_purchase_amount' => 500,
            'max_purchase_amount' => 1000,
        ]));
        $feePlanList->add(new FeePlan([
            'installments_count'  => 3,
            'deferred_months'     => 0,
            'deferred_days'       => 0,
            'min_purchase_amount' => 500,
            'max_purchase_amount' => 1000,
        ]));
        $feePlanList->add(new FeePlan([
            'installments_count'  => 4,
            'deferred_months'     => 0,
            'deferred_days'       => 0,
            'min_purchase_amount' => 500,
            'max_purchase_amount' => 1000,
        ]));
        // Credit plans
        $feePlanList->add(new FeePlan([
            'installments_count'  => 10,
            'deferred_months'     => 0,
            'deferred_days'       => 0,
            'min_purchase_amount' => 500,
            'max_purchase_amount' => 1000,
        ]));
        $feePlanList->add(new FeePlan([
            'installments_count'  => 12,
            'deferred_months'     => 0,
            'deferred_days'       => 0,
            'min_purchase_amount' => 500,
            'max_purchase_amount' => 1000,
        ]));

        $this->assertInstanceOf(FeePlan::class, $feePlanList->getByPlanKey('general_2_0_0'));
        $this->assertCount(3, $feePlanList->filterFeePlanList(['pnx']));
        $this->assertCount(2, $feePlanList->filterFeePlanList(['credit']));
        $this->assertCount(2, $feePlanList->filterFeePlanList(['pay-later']));
        $this->assertCount(1, $feePlanList->filterFeePlanList(['pay-now']));
        $this->assertCount(0, $feePlanList->filterFeePlanList(['never']));
    }

}
