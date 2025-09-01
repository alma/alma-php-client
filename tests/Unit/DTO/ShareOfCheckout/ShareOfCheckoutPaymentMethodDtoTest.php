<?php

namespace Alma\API\Tests\Unit\DTO\ShareOfCheckout;

use Alma\API\DTO\ShareOfCheckout\ShareOfCheckoutOrderDto;
use Alma\API\DTO\ShareOfCheckout\ShareOfCheckoutPaymentMethodDto;
use PHPUnit\Framework\TestCase;

class ShareOfCheckoutPaymentMethodDtoTest extends TestCase
{
    public function testShareOfCheckoutPaymentMethodDto()
    {
        $data = [
            "payment_method_name" => "Alma",
            "orders" => [
                [
                    "order_count" => 60,
                    "amount" => 30000,
                    "currency" => "EUR"
                ]
            ]
        ];

        $shareOfCheckoutOrderDto = new ShareOfCheckoutOrderDto(
            $data['orders'][0]['order_count'],
            $data['orders'][0]['amount'],
            $data['orders'][0]['currency']
        );

        $shareOfCheckoutPaymentMethodDto = (new ShareOfCheckoutPaymentMethodDto(
            $data['payment_method_name']
        ))
            ->addOrder($shareOfCheckoutOrderDto);

        $this->assertEquals($data, $shareOfCheckoutPaymentMethodDto->toArray());
    }

}