<?php

namespace Alma\Client\Tests\Unit\Application\DTO;

use Alma\Client\Application\DTO\CartDto;
use Alma\Client\Application\DTO\CartItemDto;
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