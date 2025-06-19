<?php

namespace Alma\API\Tests\Unit\Entities;

use Alma\API\Entities\FeePlan;
use PHPUnit\Framework\TestCase;

class FeePlanTest extends TestCase
{
    public function testConstructorSetsValuesCorrectly()
    {
        $feePlan = (new FeePlan([
            'deferred_trigger_limit_days' => 0,
            'kind'                        => 'general',
            'max_purchase_amount'         => 1000,
        ]))->setDeferredMonths(5)
           ->setDeferredDays(3)
           ->setInstallmentsCount(4);

        $this->assertEquals('general', $feePlan->getKind());
        $this->assertEquals(4, $feePlan->getInstallmentsCount());
        $this->assertEquals(3, $feePlan->getDeferredDays());
        $this->assertEquals(5, $feePlan->getDeferredMonths());
        $this->assertEquals(0, $feePlan->getDeferredTriggerLimitDays());
        $this->assertEquals(1000, $feePlan->getMaxPurchaseAmount());
    }
}