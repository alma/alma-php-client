<?php

namespace Alma\Client\Tests\Unit\Application\DTO;

use Alma\Client\Application\DTO\RequestedProductDto;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RequestedProductDtoTest extends TestCase
{
    public function testInstallmentsOnly(): void
    {
        $this->assertSame(['installments_count' => 3], (new RequestedProductDto(3))->toArray());
    }

    public function testDeferredDays(): void
    {
        $dto = (new RequestedProductDto(1))->setDeferredDays(30);

        $this->assertSame(['installments_count' => 1, 'deferred_days' => 30], $dto->toArray());
    }

    public function testZeroDeferralIsSent(): void
    {
        $dto = (new RequestedProductDto(3))->setDeferredDays(0);

        $this->assertSame(['installments_count' => 3, 'deferred_days' => 0], $dto->toArray());
    }

    public function testInstallmentsCountMustBePositive(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RequestedProductDto(0);
    }

    public function testDeferredDaysCannotBeNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new RequestedProductDto(1))->setDeferredDays(-1);
    }
}
