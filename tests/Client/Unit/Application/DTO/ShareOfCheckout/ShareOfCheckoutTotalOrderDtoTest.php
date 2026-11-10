<?php

namespace Alma\Client\Tests\Unit\Application\DTO\ShareOfCheckout;

use Alma\Client\Application\DTO\ShareOfCheckout\ShareOfCheckoutTotalOrderDto;
use PHPUnit\Framework\TestCase;

class ShareOfCheckoutTotalOrderDtoTest extends TestCase
{
    public function testShareOfCheckoutTotalOrderDto(): void
    {
        $data = [
            "total_order_count" => 100,
            "total_amount" => 50000,
            "currency" => "EUR"
        ];

        $shareOfCheckoutTotalOrderDto = (new ShareOfCheckoutTotalOrderDto(
            $data['total_order_count'],
            $data['total_amount'],
            $data['currency']
        ));

        $this->assertEquals($data, $shareOfCheckoutTotalOrderDto->toArray());

    }

}
