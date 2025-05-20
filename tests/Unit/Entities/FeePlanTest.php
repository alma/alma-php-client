<?php

namespace Alma\API\Tests\Unit\Entities;

use Alma\API\Entities\FeePlan;
use PHPUnit\Framework\TestCase;

class FeePlanTest extends TestCase
{
    public function testConstructorSetsValuesCorrectly()
    {
        $feePlan = new FeePlan([
            'deferredDays' => 0,
            'deferredMonths' => 3,
            'deferredTriggerLimitDays' => 0,
            'installmentsCount' => 0,
            'kind' => 'general',
        ]);

        $this->assertEquals('general', $feePlan->getKind());
        $this->assertEquals(0, $feePlan->getInstallmentsCount());
        $this->assertEquals(0, $feePlan->getDeferredDays());
        $this->assertEquals(3, $feePlan->getDeferredMonths());
        $this->assertEquals(0, $feePlan->getDeferredTriggerLimitDays());
    }
}