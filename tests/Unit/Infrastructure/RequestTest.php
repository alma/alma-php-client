<?php

namespace Alma\API\Tests\Unit\Infrastructure;

use Alma\API\Helper\StreamHelper;
use Alma\API\Infrastructure\Exception\RequestException;
use Alma\API\Infrastructure\Request;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    public function stringToStream(string $string): Stream
    {
        $resourceFromString = fopen('php://temp', 'r+');
        fwrite($resourceFromString, $string);
        fseek($resourceFromString, 0);
        return new Stream($resourceFromString);
    }

    /**
     * @throws RequestException
     */
    public function testBody()
    {
        $request = new Request("GET", "https://example.com", [], 'the body');
        $this->assertInstanceOf(Stream::class, $request->getBody());
        $request->withBody($this->stringToStream("the new body"));
        $this->assertEquals("the new body", $request->getBody()->getContents());
    }

    /**
     * @throws RequestException
     */
    public function testTargetAndUri()
    {
        $request = new Request("GET", "https://example.com", [], 'the body');
        $this->assertEquals('/', $request->getRequestTarget());
        $request->withRequestTarget('/my-endpoint');
        $this->assertEquals('/my-endpoint', $request->getRequestTarget());
        $request->withUri(new Uri('https://new-example.com'));
        $this->assertEquals('https://new-example.com', (string)$request->getUri());
        $request->withUri(new Uri('https://new-example.com/my-endpoint?foo=bar'));
        $this->assertEquals('/my-endpoint?foo=bar', $request->getRequestTarget());
    }

    public function testMethod()
    {
        $request = new Request("GET", "https://example.com", [], 'the body');
        $this->assertEquals('GET', $request->getMethod());
        $request->withMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
        $this->expectException(InvalidArgumentException::class);
        $request->withMethod('TEST');
        $this->expectException(InvalidArgumentException::class);
        $request->validateMethod('TEST');
    }

    public function testProtocolVersion()
    {
        $request = new Request("GET", "https://example.com", [], 'the body');
        $this->assertEquals('1.1', $request->getProtocolVersion());
        $request->withProtocolVersion('2.0');
        $this->assertEquals('2.0', $request->getProtocolVersion());
    }

    public function testHeaders()
    {
        // Request
        $request = new Request("GET", "https://example.com", ['my-header' => ['my-value']], 'the body');
        $this->assertEquals(['my-header' => ['my-value']], $request->getHeaders());
        $request->withHeader('my-header', ['my-value']);
        $this->assertTrue($request->hasHeader('my-header'));
        $this->assertEquals(['my-header' => ['my-value']], $request->getHeaders());
        $this->assertEquals('my-value', $request->getHeaderLine('my-header'));
        $request->withAddedHeader('my-new-header', 'my-new-value');
        $this->assertEquals(['my-header' => ['my-value'], 'my-new-header' => ['my-new-value']], $request->getHeaders());
        $request->withoutHeader('my-header');
        $this->assertFalse($request->hasHeader('my-header'));
    }

    public function testValidateProtocolVersion()
    {
        // Expectation
        $this->expectException(\InvalidArgumentException::class);

        // Response
        $request = new Request("GET", "https://example.com", [], 'the body');
        $request->validateProtocolVersion('0.1');
    }
}