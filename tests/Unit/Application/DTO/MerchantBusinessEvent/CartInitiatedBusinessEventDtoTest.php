<?php

namespace Alma\API\Tests\Unit\Application\DTO\MerchantBusinessEvent;

use Alma\API\Application\DTO\MerchantBusinessEvent\CartInitiatedBusinessEventDto;
use Alma\API\Infrastructure\Exception\ParametersException;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class CartInitiatedBusinessEventDtoTest extends MockeryTestCase
{

    public function testCartInitiatedBusinessEventData()
    {
        $data = [
            'event_type' => 'cart_initiated',
            'cart_id' => '54'
        ];
        $event = new CartInitiatedBusinessEventDto('54');
        $this->assertEquals($data, $event->toArray());
    }

    public function testCartInitiatedBusinessEventBadData()
    {
        $this->expectException(ParametersException::class);
        new CartInitiatedBusinessEventDto('');
    }
}
