<?php

namespace Alma\Client\Tests\Unit\Domain\Entity;

use Alma\Client\Domain\Entity\FeePlan;
use Alma\Client\Domain\Entity\FeePlanList;
use Alma\Plugin\Infrastructure\Adapter\FeePlanListInterface;
use PHPUnit\Framework\TestCase;

class FeePlanListTest extends TestCase
{
    private function feePlanFactory(bool $allowed): FeePlan
    {
        $feePlan = $this->createMock(FeePlan::class);
        $feePlan->method('isAllowed')->willReturn($allowed);
        return $feePlan;
    }

    public function testFilterAllowedReturnsOnlyAllowedFeePlans(): void
    {
        $feePlanList = new FeePlanList();
        $feePlanList->add($this->feePlanFactory(true));
        $feePlanList->add($this->feePlanFactory(true));
        $feePlanList->add($this->feePlanFactory(false));

        $result = $feePlanList->filterAllowed();

        $this->assertCount(2, $result);
    }

    public function testFilterAllowedReturnsEmptyListWhenNoneAllowed(): void
    {
        $feePlanList = new FeePlanList();
        $feePlanList->add($this->feePlanFactory(false));
        $feePlanList->add($this->feePlanFactory(false));

        $result = $feePlanList->filterAllowed();

        $this->assertCount(0, $result);
    }


    public function testFilterAllowedOnEmptyListReturnsEmptyList(): void
    {
        $feePlanList = new FeePlanList();

        $result = $feePlanList->filterAllowed();

        $this->assertCount(0, $result);
    }

}
