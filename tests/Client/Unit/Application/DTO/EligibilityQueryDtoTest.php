<?php

namespace Alma\Client\Tests\Unit\Application\DTO;

use Alma\Client\Application\DTO\EligibilityQueryDto;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class EligibilityQueryDtoTest extends TestCase
{
    public function testEligibilityDto()
    {
        $data = [
            'installments_count'        => 3,
            'deferred_days'             => 0,
            'deferred_months'           => 0,
        ];

        $eligibilityDto = (new EligibilityQueryDto(3))
            ->setDeferredDays(0)
            ->setDeferredMonths(0);

        $this->assertEquals($data, $eligibilityDto->toArray());
    }

    public function testSetNegativeInstallmentsCountThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Installments count must be positive.");
        new EligibilityQueryDto(-1);
    }

    public function testSetNegativeDeferredDaysThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Deferred days count must be positive.");
        (new EligibilityQueryDto(6))->setDeferredDays(-5);
    }

    public function testSetNegativeDeferredMonthsThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Deferred months must be positive.");
        (new EligibilityQueryDto(6))->setDeferredMonths(-2);
    }
}