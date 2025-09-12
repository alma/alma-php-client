<?php

namespace Alma\API\Tests\Unit\DTO;

use Alma\API\DTO\RefundDto;
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
            ->setMerchantReference('order_123_refund_1')
            ->setComment('Customer comment');
        $this->assertEquals(['amount' => 5000, 'merchant_reference' => 'order_123_refund_1', 'comment' => 'Customer comment'], $this->refund->toArray());
    }
}