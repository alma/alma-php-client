<?php

namespace Alma\API\Tests\Unit\Entities\DTO\MerchantBusinessEvent;

use Alma\API\Entities\DTO\MerchantBusinessEvent\CartInitiatedBusinessEvent;
use Alma\API\Exceptions\ParametersException;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use stdClass;

class CartInitiatedBusinessEventTest extends MockeryTestCase
{

    public function testCartInitiatedBusinessEventData()
    {
        $event = new CartInitiatedBusinessEvent('54');
        $this->assertEquals('cart_initiated', $event->getEventType());
        $this->assertEquals('54', $event->getCartId());
    }
}
