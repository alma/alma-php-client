<?php

namespace Alma\API\Tests\Unit\Application\DTO;

use Alma\API\Application\DTO\CartItemDto;
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

        $cartItemDto = (new CartItemDto($data['quantity'], $data['line_price'], $data['picture_url']))
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
        (new CartItemDto(1, 25, 'https://example.com/image.jpg'))->setQuantity(0);
    }

    public function testInvalidUnitPrice()
    {
        $this->expectException(InvalidArgumentException::class);
        (new CartItemDto(1, 25, 'https://example.com/image.jpg'))->setUnitPrice(-1);
    }

    public function testInvalidLinePrice()
    {
        $this->expectException(InvalidArgumentException::class);
        (new CartItemDto(1, 25, 'https://example.com/image.jpg'))->setLinePrice(-1);
    }

    public function testInvalidUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        (new CartItemDto(1, 25, 'https://example.com/image.jpg'))->setUrl('invalid-url');
    }

    public function testInvalidPictureUrl()
    {
        $this->expectException(InvalidArgumentException::class);
        (new CartItemDto(1, 25, 'https://example.com/image.jpg'))->setPictureUrl('invalid-url');
    }
}