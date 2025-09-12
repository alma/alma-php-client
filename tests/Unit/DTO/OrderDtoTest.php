<?php

namespace Alma\API\Tests\Unit\DTO;

use Alma\API\Application\DTO\OrderDto;
use PHPUnit\Framework\TestCase;

class OrderDtoTest extends TestCase
{
    public function testCartItemDto()
    {
        $data = [
            'merchant_reference' => 'order_123',
            'merchant_url' => 'https://example.com/order/123',
            'customer_url' => 'https://example.com/customer/456',
            'comment' => 'Please deliver by next week',
        ];

        $orderDto = (new OrderDto())
            ->setMerchantReference($data['merchant_reference'])
            ->setMerchantUrl($data['merchant_url'])
            ->setCustomerUrl($data['customer_url'])
            ->setComment($data['comment']);

        $this->assertEquals($data, $orderDto->toArray());
    }

    public function testInvalidCustomerUrl()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new OrderDto())->setCustomerUrl('invalid-url');
    }

    public function testInvalidMerchantUrl()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new OrderDto())->setMerchantUrl('invalid-url');
    }
}