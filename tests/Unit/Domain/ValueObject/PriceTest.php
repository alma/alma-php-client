<?php

namespace Alma\API\Tests\Unit\Domain\ValueObject;

use Alma\API\Domain\ValueObject\Price;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PriceTest extends TestCase
{
    public function testPriceWithNegativeValueThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Price value must be non-negative.");
        new Price(-100, Price::EUROCENTS);
    }

    public function testPriceWithUnsupportedCurrencyThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Unsupported currency: USD");
        new Price(100, 'USD');
    }

    public function testPriceEqualityReturnsTrueForSameValueAndCurrency()
    {
        $price1 = new Price(100, Price::EUROCENTS);
        $price2 = new Price(100, Price::EUROCENTS);

        $this->assertTrue($price1->equals($price2));
    }

    public function testPriceEqualityReturnsFalseForDifferentValues()
    {
        $price1 = new Price(100, Price::EUROCENTS);
        $price2 = new Price(200, Price::EUROCENTS);

        $this->assertFalse($price1->equals($price2));
    }

    public function testToStringReturnsFormattedPrice()
    {
        $price = new Price(100, Price::EUROCENTS);
        $this->assertEquals('100 EUROCENTS', (string)$price);
    }
}
