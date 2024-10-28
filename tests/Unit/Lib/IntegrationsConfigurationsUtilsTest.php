<?php

namespace Unit\Lib;

use Alma\API\Lib\IntegrationsConfigurationsUtils;
use PHPUnit\Framework\TestCase;

class IntegrationsConfigurationsUtilsTest extends TestCase
{

    /**
     * @var IntegrationsConfigurationsUtils
     */
    private $integrationsConfigurationsUtils;

    public function setUp() : void
    {
     $this->integrationsConfigurationsUtils = new IntegrationsConfigurationsUtils();
    }

    public function testNewSendIsNotNecessary()
    {
        $timestamp = time() - 100;
        $this->assertFalse($this->integrationsConfigurationsUtils->isUrlRefreshRequired($timestamp));
    }
    public function testNewSendIsNecessary()
    {
        $oneMonthInSecondsMoreTen = 30 * 24 * 60 * 60 + 10; // 30 jours en secondes +10 sec
        $timestamp = time() - $oneMonthInSecondsMoreTen;
        $this->assertTrue($this->integrationsConfigurationsUtils->isUrlRefreshRequired($timestamp));
    }

    public function testNewSendIsNecessaryWithValueNull()
    {
        $this->assertTrue($this->integrationsConfigurationsUtils->isUrlRefreshRequired(null));
    }
}