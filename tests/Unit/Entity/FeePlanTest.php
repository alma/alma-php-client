<?php

namespace Alma\API\Tests\Unit\Entity;

use Alma\API\Entity\FeePlan;
use PHPUnit\Framework\TestCase;

class FeePlanTest extends TestCase
{
    public function testConstructorSetsValuesCorrectly()
    {
        $feePlan = (new FeePlan([
            'deferred_trigger_limit_days' => 0,
            'kind'                        => 'general',
            'min_purchase_amount'         => 500,
            'max_purchase_amount'         => 1000,
            'deferred_months'             => 5,
            'deferred_days'               => 3,
            'installments_count'          => 4,
        ]));

        $this->assertEquals('general', $feePlan->getKind());
        $this->assertEquals(4, $feePlan->getInstallmentsCount());
        $this->assertEquals(3, $feePlan->getDeferredDays());
        $this->assertEquals(5, $feePlan->getDeferredMonths());
        $this->assertEquals(0, $feePlan->getDeferredTriggerLimitDays());
        $this->assertEquals(500, $feePlan->getMinPurchaseAmount());
        $this->assertEquals(1000, $feePlan->getMaxPurchaseAmount());
    }
}