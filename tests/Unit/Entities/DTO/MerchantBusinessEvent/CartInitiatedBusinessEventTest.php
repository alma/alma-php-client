<?php

namespace Alma\API\Tests\Unit\Entities\DTO\MerchantBusinessEvent;

use Alma\API\Entities\DTO\MerchantBusinessEvent\CartInitiatedBusinessEvent;
use Alma\API\Exceptions\ParametersException;
use PHPUnit\Framework\TestCase;

class CartInitiatedBusinessEventTest extends TestCase
{

    public function testCartInitiatedBusinessEventData()
    {
        $event = new CartInitiatedBusinessEvent('54');
        $this->assertEquals('cart_initiated', $event->getEventType());
        $this->assertEquals('54', $event->getCartId());
    }

    /**
     * @dataProvider invalidDataForBusinessEventDataProvider
     * @param $cartId
     */
    public function testInvalidDataForBusinessEvent($cartId)
    {
        $this->expectException(ParametersException::class);
        $this->expectExceptionMessage('CartId must be a string');
        new CartInitiatedBusinessEvent($cartId);
    }
    public static function invalidDataForBusinessEventDataProvider()
    {
        return [
            "cartId is an int" => [1],
            "cartId is a float" => [1.1],
            "cartId is an array" => [[]],
            "cartId is an object" => [new \stdClass()],
            "cartId is a boolean" => [true],
            "cartId is null" => [null],
        ];
    }

}
