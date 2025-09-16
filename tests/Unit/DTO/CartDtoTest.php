<?php

namespace Alma\API\Tests\Unit\DTO;

use Alma\API\Application\DTO\CartDto;
use Alma\API\Application\DTO\CartItemDto;
use PHPUnit\Framework\TestCase;

class CartDtoTest extends TestCase
{
    public function testCanAddItemToCartDto():void
    {
        $item = $this->createMock(CartItemDto::class);
        $cartDto = new CartDto();
        $cartDto->addItem($item);
        $this->assertSame(['items' => [$item->toArray()]], $cartDto->toArray());
    }

}