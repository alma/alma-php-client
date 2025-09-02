<?php

namespace Alma\API\Tests\Unit\DTO\MerchantBusinessEvent;

use Alma\API\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEventDto;
use Alma\API\Exception\ParametersException;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OrderConfirmedBusinessEventDtoTest extends MockeryTestCase
{

    /**
     * @throws ParametersException
     */
    public function testOrderConfirmedBusinessEventDataForNonAlmaPayment()
    {
        $data = [
            'event_type' => 'order_confirmed',
            'is_alma_p1x' => false,
            'is_alma_bnpl' => false,
            'was_bnpl_eligible' => true,
            'order_id' => "42",
            'cart_id' => "54",
        ];

        $event = new OrderConfirmedBusinessEventDto(false, false, true, '42', '54');
        $this->assertEquals($data, $event->toArray());
    }

    public function testAlmaPaymentIdIsMandatoryForP1xAlmaPayment()
    {
        $this->expectException(ParametersException::class);

        new OrderConfirmedBusinessEventDto(true, false, true, "42", "54");
    }

    public function testAlmaPaymentIdIsMandatoryForBnplAlmaPayment()
    {
        $this->expectException(ParametersException::class);

        new OrderConfirmedBusinessEventDto(false, true, true, "42", "54");
    }
    public function testAlmaPaymentIdCanNotBeAnEmptyStringForAnAlmaPayment()
    {
        $this->expectException(ParametersException::class);
        new OrderConfirmedBusinessEventDto(false, true, true, "42", "54", "");
    }

    public function testAlmaPaymentIdShouldBeAbsentForNonAlmaPayments()
    {
        $this->expectException(ParametersException::class);

        new OrderConfirmedBusinessEventDto(
            false,
            false,
            true,
            "42",
            "54",
            'alma_payment_id'
        );
    }

    /**
     * @throws ParametersException
     */
    public function testAlmaPaymentIdDataForAlmaPayment()
    {
        $data = [
            [
                'event_type' => 'order_confirmed',
                'is_alma_p1x' => true,
                'is_alma_bnpl' => false,
                'was_bnpl_eligible' => true,
                'order_id' => '42',
                'cart_id' => '54',
                'alma_payment_id' => 'almaPaymentId'
            ],
            [
                'event_type' => 'order_confirmed',
                'is_alma_p1x' => false,
                'is_alma_bnpl' => true,
                'was_bnpl_eligible' => true,
                'order_id' => '43',
                'cart_id' => '55',
                'alma_payment_id' => 'alma_payment_id'
            ],
        ];
        foreach ($data as $item) {
            $orderConfirmedEvent = new OrderConfirmedBusinessEventDto(
                $item['is_alma_p1x'],
                $item['is_alma_bnpl'],
                $item['was_bnpl_eligible'],
                $item['order_id'],
                $item['cart_id'],
                $item['alma_payment_id']
            );
            $this->assertEquals($item, $orderConfirmedEvent->toArray());
        }
    }
}
