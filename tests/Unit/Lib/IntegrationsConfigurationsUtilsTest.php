<?php

namespace Unit\Lib;

use Alma\API\Lib\IntegrationsConfigurationsUtils;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class IntegrationsConfigurationsUtilsTest extends MockeryTestCase
{

    public function testNewSendIsNotNecessary()
    {
        $timestamp = time() - 100;
        $this->assertFalse(IntegrationsConfigurationsUtils::isUrlRefreshRequired($timestamp));
    }
    public function testNewSendIsNecessary()
    {
        $oneMonthInSecondsMoreTen = 30 * 24 * 60 * 60 + 10; // 30 days in seconds +10 sec
        $timestamp = time() - $oneMonthInSecondsMoreTen;
        $this->assertTrue(IntegrationsConfigurationsUtils::isUrlRefreshRequired($timestamp));
    }

    public function testNewSendIsNecessaryWithValueNull()
    {
        $this->assertTrue(IntegrationsConfigurationsUtils::isUrlRefreshRequired(null));
    }
}