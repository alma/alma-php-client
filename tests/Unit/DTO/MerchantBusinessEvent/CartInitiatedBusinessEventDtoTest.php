<?php

namespace Alma\API\Tests\Unit\DTO\MerchantBusinessEvent;

use Alma\API\DTO\MerchantBusinessEvent\CartInitiatedBusinessEventDtoDto;
use Alma\API\Exception\ParametersException;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CartInitiatedBusinessEventDtoTest extends MockeryTestCase
{

    public function testCartInitiatedBusinessEventData()
    {
        $event = new CartInitiatedBusinessEventDtoDto('54');
        $this->assertEquals('cart_initiated', $event->getEventType());
        $this->assertEquals('54', $event->getCartId());
    }

    public function testCartInitiatedBusinessEventBadData()
    {
        $this->expectException(ParametersException::class);
        new CartInitiatedBusinessEventDtoDto('');
    }
}
