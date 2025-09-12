<?php

namespace Alma\API\Tests\Unit\Entity;

use Alma\API\Domain\Entity\FeePlan;
use Alma\API\Domain\Entity\FeePlanList;
use Alma\API\Infrastructure\Exception\ParametersException;
use PHPUnit\Framework\TestCase;

class FeePlanListTest extends TestCase
{
    /**
     * @throws ParametersException
     */
    public function testFeePlanList() {
        $feePlanList = new FeePlanList();

        $feePlan = $this->feePlanFactory(0,0,1);

        // Pay now plans
        $feePlanList->add($this->feePlanFactory(1,0,0)) ;
        // Pay-later plans
        $feePlanList->add( $this->feePlanFactory(1,2,0));
        $feePlanList->add($this->feePlanFactory(1,0,15));
        // Pnx plans
        $feePlanList->add($this->feePlanFactory(2,0,0));
        $feePlanList->add($this->feePlanFactory(3,0,0));
        $feePlanList->add($this->feePlanFactory(4,0, 0));
        // Credit plans
        $feePlanList->add($this->feePlanFactory(10,0, 0));
        $feePlanList->add($this->feePlanFactory(12,0, 0));

        $this->assertInstanceOf(FeePlan::class, $feePlanList->getByPlanKey('general_2_0_0'));
        $this->assertCount(3, $feePlanList->filterFeePlanList(['pnx']));
        $this->assertCount(2, $feePlanList->filterFeePlanList(['credit']));
        $this->assertCount(2, $feePlanList->filterFeePlanList(['pay-later']));
        $this->assertCount(1, $feePlanList->filterFeePlanList(['pay-now']));
        $this->assertCount(0, $feePlanList->filterFeePlanList(['never']));
    }

    /**
     * @throws ParametersException
     */
    private function feePlanFactory(int $installments, int $deferredDays, int $deferredMonths ): FeePlan{
        return new FeePlan([
            'allowed'             => true,
            'available_online'    => true,
            'customer_fee_variable'=> 380,
            'deferred_days'       => $deferredDays,
            'deferred_months'     => $deferredMonths,
            'installments_count'  => $installments,
            'kind'                => 'general',
            'min_purchase_amount' => 500,
            'merchant_fee_variable'=> 12,
            'merchant_fee_fixed'  => 18,
            'max_purchase_amount' => 1000,
        ]);
    }
}
