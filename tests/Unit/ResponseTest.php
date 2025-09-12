<?php

namespace Alma\API\Tests\Unit;

use Alma\API\Infrastructure\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function stringToStream(string $string): Stream
    {
        $resourceFromString = fopen('php://temp', 'r+');
        fwrite($resourceFromString, $string);
        fseek($resourceFromString, 0);
        return new Stream($resourceFromString);
    }

    public function testStatusCode()
    {
        // Response
        $response = new Response('200', 'the body', ['my-header' => ['my-value']]);
        $this->assertEquals(200, $response->getStatusCode());
        $response->withStatus(404);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testHeaders()
    {
        // Response
        $response = new Response('200', 'the body', ['my-header' => ['my-value']]);
        $this->assertEquals(['my-header' => ['my-value']], $response->getHeaders());
        $response->withHeader('my-header', ['my-value']);
        $this->assertTrue($response->hasHeader('my-header'));
        $this->assertEquals(['my-header' => ['my-value']], $response->getHeaders());
        $this->assertEquals('my-value', $response->getHeaderLine('my-header'));
        $response->withAddedHeader('my-new-header', 'my-new-value');
        $this->assertEquals(['my-header' => ['my-value'], 'my-new-header' => ['my-new-value']], $response->getHeaders());
        $response->withoutHeader('my-header');
        $this->assertFalse($response->hasHeader('my-header'));
    }

    public function testReasonPhrase()
    {
        // Response
        $response = new Response('200', 'the body', ['my-header' => ['my-value']]);
        $this->assertEquals('OK', $response->getReasonPhrase());
    }

    public function testIsError()
    {
        // Response
        $response = new Response('200', 'the body', ['my-header' => ['my-value']]);
        $this->assertFalse($response->isError());
        $response = new Response('500', 'the body', ['my-header' => ['my-value']]);
        $this->assertTrue($response->isError());
    }

    public function testBody()
    {
        // Response
        $response = new Response('200', '{"foo":"bar"} ', ['my-header' => ['my-value']]);
        $this->assertInstanceOf(Stream::class, $response->getBody());
        $response->withBody($this->stringToStream('{"foo":"bar"} '));
        $this->assertEquals('{"foo":"bar"} ', $response->getBody()->getContents());
        $this->assertEquals('{"foo":"bar"} ', $response->getFile());
        $this->assertEquals(["foo" => "bar"], $response->getJson());
    }

    public function testProtocolVersion()
    {
        // Response
        $response = new Response('200', 'the body', ['my-header' => ['my-value']], '1.1');
        $this->assertEquals('1.1', $response->getProtocolVersion());
        $response->withProtocolVersion('2.0');
        $this->assertEquals('2.0', $response->getProtocolVersion());
    }

    public function testValidateProtocolVersion()
    {
        // Expectation
        $this->expectException(\InvalidArgumentException::class);

        // Response
        new Response('200', 'the body', ['my-header' => ['my-value']], '0.1');
    }
}