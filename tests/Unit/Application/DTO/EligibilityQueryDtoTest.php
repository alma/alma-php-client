<?php

namespace Alma\API\Tests\Unit\Application\DTO;

use Alma\API\Application\DTO\AddressDto;
use Alma\API\Application\DTO\EligibilityDto;
use Alma\API\Application\DTO\EligibilityQueryDto;
use Alma\API\Application\DTO\PaymentDto;
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

    public function testSetInvalidInstallmentsCountThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Installments count must be between 1 and 12.");
        new EligibilityQueryDto(13);
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