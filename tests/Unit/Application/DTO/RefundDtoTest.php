<?php

namespace Alma\API\Tests\Unit\Application\DTO;

use Alma\API\Application\DTO\RefundDto;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RefundDtoTest extends TestCase
{

    private RefundDto $refund;

    protected function setUp(): void
    {
        $this->refund = new RefundDto();
    }

    protected function tearDown(): void
    {
        unset($this->refund);
    }

    public function testFullRefundDefaultConstructDto()
    {
        $this->assertEquals([], $this->refund->toArray());
    }

    public function testPartialRefundConstructDto()
    {
        $this->refund->setAmount(5000)
            ->setMerchantReference('order_123_refund_2')
            ->setComment('Customer comment');
        $this->assertEquals(['amount' => 5000, 'merchant_reference' => 'order_123_refund_2', 'comment' => 'Customer comment'], $this->refund->toArray());
    }

    public function testSetInvalidRefundAmountThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Refund amount cannot be negative or zero.");
        $this->refund->setAmount(-5000);
    }

    public function testSetZeroRefundAmountThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Refund amount cannot be negative or zero.");
        $this->refund->setAmount(0);
    }
}
