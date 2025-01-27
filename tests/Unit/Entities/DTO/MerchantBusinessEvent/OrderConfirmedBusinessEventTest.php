<?php

namespace Alma\API\Tests\Unit\Entities\DTO\MerchantBusinessEvent;

use Alma\API\Entities\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEvent;
use Alma\API\Exceptions\ParametersException;
use PHPUnit\Framework\TestCase;

class OrderConfirmedBusinessEventTest extends TestCase
{

    public function testOrderConfirmedBusinessEventDataForNonAlmaPayment()
    {
        $isAlmaP1X = false;
        $isAlmaBNPL = false;
        $wasBNPLEligible = true;
        $orderId = "42";
        $cartId = "54";
        $event = new OrderConfirmedBusinessEvent($isAlmaP1X, $isAlmaBNPL, $wasBNPLEligible, $orderId, $cartId);
        $this->assertEquals('order_confirmed', $event->getEventType());
        $this->assertEquals($isAlmaP1X, $event->isAlmaP1X());
        $this->assertEquals($isAlmaBNPL, $event->isAlmaBNPL());
        $this->assertEquals($wasBNPLEligible, $event->wasBNPLEligible());
        $this->assertEquals($orderId, $event->getOrderId());
        $this->assertEquals($cartId, $event->getCartId());
        $this->assertNull($event->getAlmaPaymentId());
    }

    /**
     * @dataProvider invalidDataForBusinessEventDataProvider
     * @param $isP1X
     * @param $isBNPL
     * @param $wasEligible
     * @param $orderId
     * @param $cartId
     * @throws ParametersException
     */
    public function testInvalidDataForBusinessEvent($isP1X, $isBNPL, $wasEligible, $orderId, $cartId)
    {
        $this->expectException(ParametersException::class);
        new OrderConfirmedBusinessEvent($isP1X, $isBNPL, $wasEligible, $orderId, $cartId);
    }

    public function testAlmaPaymentIdIsMandatoryForP1xAlmaPayment()
    {
        $this->expectException(ParametersException::class);
        new OrderConfirmedBusinessEvent(true, false, true, "42", "54");
    }

    public function testAlmaPaymentIdIsMandatoryForBnplAlmaPayment()
    {
        $this->expectException(ParametersException::class);
        new OrderConfirmedBusinessEvent(false, true, true, "42", "54");
    }
    public function testAlmaPaymentIdCanNotBeAnEmptyStringForAnAlmaPayment()
    {
        $this->expectException(ParametersException::class);
        new OrderConfirmedBusinessEvent(false, true, true, "42", "54", "");
    }

    public function testAlmaPaymentIdShouldBeAbsentForNonAlmaPayments()
    {
        $this->expectException(ParametersException::class);
        new OrderConfirmedBusinessEvent(
            false,
            false,
            true,
            "42",
            "54",
            'alma_payment_id'
        );
    }

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
            $orderConfirmedEvent = new OrderConfirmedBusinessEvent(
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

    public static function invalidDataForBusinessEventDataProvider()
    {
        return [
            "isAlmaP1X is an int" => [
                'isP1X' => 1,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "isAlmaP1X is an float" => [
                'isP1X' => 1.1,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "isAlmaP1X is an array" => [
                'isP1X' => [],
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "isAlmaP1X is a string" => [
                'isP1X' => "1",
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "isAlmaP1X is a class" => [
                'isP1X' => new \stdClass(),
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "isBNPL is an int" => [
                'isP1X' => false,
                'isBNPL' => 1,
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "isBNPL is an float" => [
                'isP1X' => false,
                'isBNPL' => 1.1,
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "isBNPL is an array" => [
                'isP1X' => false,
                'isBNPL' => [],
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "isBNPL is a string" => [
                'isP1X' => false,
                'isBNPL' => '42',
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "isBNPL is a class" => [
                'isP1X' => false,
                'isBNPL' => new \stdClass(),
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "was Eligible is an int" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => 1,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "was Eligible is an float" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => 1.1,
                'orderId' => "1",
                'cartId' => "1"
            ],
            "was Eligible is an array" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => [],
                'orderId' => "1",
                'cartId' => "1"
            ],
            "was Eligible is a string" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => '45',
                'orderId' => "1",
                'cartId' => "1"
            ],
            "was Eligible is a class" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => new \stdClass(),
                'orderId' => "1",
                'cartId' => "1"
            ],
            "Order id is empty string" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => "",
                'cartId' => "14"
            ],
            "Order id is an int" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => 1,
                'cartId' => "1"
            ],
            "Order id is an float" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => 1.1,
                'cartId' => "1"
            ],
            "Order id is an array" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => [],
                'cartId' => "1"
            ],
            "Order id is a bool" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => true,
                'cartId' => "1"
            ],
            "Order id is a class" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => new \stdClass(),
                'cartId' => '1'
            ],
            "Cart id is an int" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => '1',
                'cartId' => 1
            ],
            "Cart id is an float" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => '1',
                'cartId' => 1.1
            ],
            "Cart id is an array" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => '1',
                'cartId' => []
            ],
            "Cart id is a bool" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => '1',
                'cartId' => true
            ],
            "Cart id is a class" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => '1',
                'cartId' => new \stdClass()
            ],
            "Cart id is empty string" => [
                'isP1X' => false,
                'isBNPL' => false,
                'wasEligible' => true,
                'orderId' => "1",
                'cartId' => ""
            ],
        ];
    }
}
