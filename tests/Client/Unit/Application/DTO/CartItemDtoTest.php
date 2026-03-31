<?php

namespace Alma\Client\Tests\Unit\Application\DTO;

use Alma\Client\Application\DTO\CartItemDto;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class CartItemDtoTest extends TestCase
{
    public function testCartItemDto()
    {
        $data = [
            'sku' => 'SKU123',
            'title' => 'My product',
            'quantity' => 5,
            'unit_price' => 25,
            'line_price' => 125,
            'categories' => ['A', 'B'],
            'url' => 'https://example.com/product',
            'picture_url' => 'https://example.com/image.jpg',
            'requires_shipping' => true,
        ];

        $cartItemDto = (new CartItemDto($data['quantity'], $data['line_price']))
            ->setSku($data['sku'])
            ->setTitle($data['title'])
            ->setQuantity($data['quantity'])
            ->setUnitPrice($data['unit_price'])
            ->setLinePrice($data['line_price'])
            ->setCategories($data['categories'])
            ->setUrl($data['url'])
            ->setPictureUrl($data['picture_url'])
            ->setRequiresShipping($data['requires_shipping']);

        $this->assertEquals($data, $cartItemDto->toArray());
    }

    public function testInvalidQuantity()
    {
        $this->expectException(InvalidArgumentException::class);
        (new CartItemDto(1, 25))->setQuantity(0);
    }

    public function testInvalidUnitPrice()
    {
        $this->expectException(InvalidArgumentException::class);
        (new CartItemDto(1, 25))->setUnitPrice(-1);
    }

    public function testInvalidLinePrice()
    {
        $this->expectException(InvalidArgumentException::class);
        (new CartItemDto(1, 25))->setLinePrice(-1);
    }

    public function testInvalidUrlIsIgnored()
    {
        $cartItemDto = (new CartItemDto(1, 25))->setUrl('invalid-url');
        $this->assertArrayNotHasKey('url', $cartItemDto->toArray());
    }

    public function testInvalidPictureUrlIsIgnored()
    {
        $cartItemDto = (new CartItemDto(1, 25))->setPictureUrl('invalid-url');
        $this->assertArrayNotHasKey('picture_url', $cartItemDto->toArray());
    }

    public function testNullPictureUrl()
    {
        $cartItemDto = new CartItemDto(1, 25);
        $result = $cartItemDto->toArray();
        $this->assertArrayNotHasKey('picture_url', $result);
    }

    public function testEmptyPictureUrl()
    {
        $cartItemDto = (new CartItemDto(1, 25))->setPictureUrl('');
        $result = $cartItemDto->toArray();
        $this->assertArrayNotHasKey('picture_url', $result);
    }

    public function testWithoutPictureUrl()
    {
        $cartItemDto = new CartItemDto(1, 25);
        $result = $cartItemDto->toArray();
        $this->assertArrayNotHasKey('picture_url', $result);
    }
}