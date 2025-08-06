<?php

namespace Alma\API\Tests\Unit\DTO\MerchantBusinessEvent;

use Alma\API\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEventDtoDto;
use Alma\API\Exception\ParametersException;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class OrderConfirmedBusinessEventDtoTest extends MockeryTestCase
{

    /**
     * @throws ParametersException
     */
    public function testOrderConfirmedBusinessEventDataForNonAlmaPayment()
    {
        $isAlmaP1X = false;
        $isAlmaBNPL = false;
        $wasBNPLEligible = true;
        $orderId = "42";
        $cartId = "54";
        $event = new OrderConfirmedBusinessEventDtoDto($isAlmaP1X, $isAlmaBNPL, $wasBNPLEligible, $orderId, $cartId);
        $this->assertEquals('order_confirmed', $event->getEventType());
        $this->assertEquals($isAlmaP1X, $event->isAlmaP1X());
        $this->assertEquals($isAlmaBNPL, $event->isAlmaBNPL());
        $this->assertEquals($wasBNPLEligible, $event->wasBNPLEligible());
        $this->assertEquals($orderId, $event->getOrderId());
        $this->assertEquals($cartId, $event->getCartId());
        $this->assertNull($event->getAlmaPaymentId());
    }

    public function testAlmaPaymentIdIsMandatoryForP1xAlmaPayment()
    {
        $this->expectException(ParametersException::class);

        new OrderConfirmedBusinessEventDtoDto(true, false, true, "42", "54");
    }

    public function testAlmaPaymentIdIsMandatoryForBnplAlmaPayment()
    {
        $this->expectException(ParametersException::class);

        new OrderConfirmedBusinessEventDtoDto(false, true, true, "42", "54");
    }
    public function testAlmaPaymentIdCanNotBeAnEmptyStringForAnAlmaPayment()
    {
        $this->expectException(ParametersException::class);
        new OrderConfirmedBusinessEventDtoDto(false, true, true, "42", "54", "");
    }

    public function testAlmaPaymentIdShouldBeAbsentForNonAlmaPayments()
    {
        $this->expectException(ParametersException::class);

        new OrderConfirmedBusinessEventDtoDto(
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
                'isP1X' => true,
                'isBNPL' => false,
                'almaPaymentId' => 'almaPaymentId'
            ],
            [
                'isP1X' => false,
                'isBNPL' => true,
                'almaPaymentId' => 'alma_payment_id'
            ],
        ];
        foreach ($data as $item) {
            $orderConfirmedEvent = new OrderConfirmedBusinessEventDtoDto(
                $item['isP1X'],
                $item['isBNPL'],
                true,
                "42",
                "54",
                $item['almaPaymentId']
            );
            $this->assertEquals($item['almaPaymentId'], $orderConfirmedEvent->getAlmaPaymentId());
        }
    }
}
