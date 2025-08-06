<?php

namespace Unit\Lib;

use Alma\API\Helper\IntegrationsConfigurationsHelper;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class IntegrationsConfigurationsHelperTest extends MockeryTestCase
{

    public function testNewSendIsNotNecessary()
    {
        $timestamp = time() - 100;
        $this->assertFalse(IntegrationsConfigurationsHelper::isUrlRefreshRequired($timestamp));
    }
    public function testNewSendIsNecessary()
    {
        $oneMonthInSecondsMoreTen = 30 * 24 * 60 * 60 + 10; // 30 days in seconds +10 sec
        $timestamp = time() - $oneMonthInSecondsMoreTen;
        $this->assertTrue(IntegrationsConfigurationsHelper::isUrlRefreshRequired($timestamp));
    }

    public function testNewSendIsNecessaryWithValueNull()
    {
        $this->assertTrue(IntegrationsConfigurationsHelper::isUrlRefreshRequired(null));
    }
}