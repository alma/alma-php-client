<?php

namespace Alma\API\Tests\Unit\Domain\ValueObject;

use Alma\API\Domain\ValueObject\Price;
use Alma\API\Domain\ValueObject\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UriTest extends TestCase
{
    public function testFromStringCreatesValidUri()
    {
        $uri = Uri::fromString('https://example.com');
        $this->assertEquals('https://example.com', $uri->getValue());
    }

    public function testFromStringThrowsExceptionForInvalidUri()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The provided string "invalid-url" is not a valid URI.');
        Uri::fromString('invalid-url');
    }

    public function testEqualsReturnsTrueForSameUri()
    {
        $uri1 = Uri::fromString('https://example.com');
        $uri2 = Uri::fromString('https://example.com');
        $this->assertTrue($uri1->equals($uri2));
    }

    public function testEqualsReturnsFalseForDifferentUris()
    {
        $uri1 = Uri::fromString('https://example.com');
        $uri2 = Uri::fromString('https://example.org');
        $this->assertFalse($uri1->equals($uri2));
    }

    public function testGetPathReturnsCorrectPath()
    {
        $uri = Uri::fromString('https://example.com/path/to/resource');
        $this->assertEquals('/path/to/resource', $uri->getPath());
    }

    public function testGetPathReturnsNullWhenNoPath()
    {
        $uri = Uri::fromString('https://example.com');
        $this->assertNull($uri->getPath());
    }

    public function testGetSchemeReturnsCorrectScheme()
    {
        $uri = Uri::fromString('https://example.com');
        $this->assertEquals('https', $uri->getScheme());
    }

    public function testToStringReturnsUriAsString()
    {
        $uri = Uri::fromString('https://example.com');
        $this->assertEquals('https://example.com', (string)$uri);
    }

}
