<?php

namespace Alma\API\Tests\Unit\DTO\ShareOfCheckout;

use Alma\API\Application\DTO\ShareOfCheckout\ShareOfCheckoutOrderDto;
use PHPUnit\Framework\TestCase;

class ShareOfCheckoutOrderDtoTest extends TestCase
{
    public function testShareOfCheckoutOrderDto()
    {
        $data = [
            "order_count" => 60,
            "amount" => 30000,
            "currency" => "EUR"
        ];

        $shareOfCheckoutOrderDto = (new ShareOfCheckoutOrderDto(
            $data['order_count'],
            $data['amount'],
            $data['currency']
        ));

        $this->assertEquals($data, $shareOfCheckoutOrderDto->toArray());
    }
}