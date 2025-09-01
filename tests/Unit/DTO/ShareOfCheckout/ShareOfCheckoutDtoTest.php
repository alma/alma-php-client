<?php

namespace Alma\API\Tests\Unit\DTO\ShareOfCheckout;

use Alma\API\DTO\ShareOfCheckout\ShareOfCheckoutDto;
use Alma\API\DTO\ShareOfCheckout\ShareOfCheckoutOrderDto;
use Alma\API\DTO\ShareOfCheckout\ShareOfCheckoutPaymentMethodDto;
use Alma\API\DTO\ShareOfCheckout\ShareOfCheckoutTotalOrderDto;
use Alma\API\Exception\ParametersException;
use PHPUnit\Framework\TestCase;

class ShareOfCheckoutDtoTest extends TestCase
{
    public function testShareOfCheckoutDto() {
        $data = [
            "start_date" => new \DateTime("2023-01-01 00:00:00"),
            "end_date" => new \DateTime("2023-01-31 23:59:59"),
            "orders" => [
                [
                    "total_order_count" => 100,
                    "total_amount" => 50000,
                    "currency" => "EUR"
                ]
            ],
            "payment_methods" => [
                [
                    "payment_method_name" => "Alma",
                    "orders" => [
                        [
                            "order_count" => 60,
                            "amount" => 30000,
                            "currency" => "EUR"
                        ]
                    ]
                ]
            ]
        ];

        $shareOfCheckoutTotalOrderDto = new ShareOfCheckoutTotalOrderDto(
            $data['orders'][0]['total_order_count'],
            $data['orders'][0]['total_amount'],
            $data['orders'][0]['currency']
        );

        $shareOfCheckoutOrderDto = new ShareOfCheckoutOrderDto(
            $data['payment_methods'][0]['orders'][0]['order_count'],
            $data['payment_methods'][0]['orders'][0]['amount'],
            $data['payment_methods'][0]['orders'][0]['currency']
        );

        $shareOfCheckoutPaymentMethodDto = (new ShareOfCheckoutPaymentMethodDto(
            $data['payment_methods'][0]['payment_method_name']
        ))
            ->addOrder($shareOfCheckoutOrderDto);

        $shareOfCheckoutDto = (new ShareOfCheckoutDto(
            $data['start_date'],
            $data['end_date']
        ))
            ->addOrder($shareOfCheckoutTotalOrderDto)
            ->addPaymentMethod($shareOfCheckoutPaymentMethodDto);

        $this->assertEquals($data, $shareOfCheckoutDto->toArray());
    }

    public function testShareOfCheckoutDtoInvalidDates() {
        $this->expectException(ParametersException::class);
        new ShareOfCheckoutDto(new \DateTime("2023-01-31"), new \DateTime("2023-01-01"));
    }
}
