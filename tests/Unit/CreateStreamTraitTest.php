<?php

namespace Alma\API\Tests\Unit;

use Alma\API\Infrastructure\StreamTrait;
use GuzzleHttp\Psr7\Stream;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;

class CreateStreamTraitTest extends TestCase
{
    private $createStreamTraitImplementation;
    public function setUp(): void
    {
        parent::setUp();
        $this->createStreamTraitImplementation = new class () {
            use StreamTrait;

            public function useCreateStreamTraitImplementation($body)
            {
                return $this->createStream($body);
            }
        };
    }

    public function testCreateStreamWithString()
    {
        $stream = $this->createStreamTraitImplementation->useCreateStreamTraitImplementation('test string');
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals('test string', (string) $stream);
    }

    public function testCreateStreamWithResource()
    {
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, 'test resource');
        rewind($resource);
        $stream = $this->createStreamTraitImplementation->useCreateStreamTraitImplementation($resource);
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals('test resource', (string) $stream);
    }

    public function testCreateStreamWithNull()
    {
        $stream = $this->createStreamTraitImplementation->useCreateStreamTraitImplementation(null);
        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals('', (string) $stream);
    }

    public function testCreateStreamWithStreamInterface()
    {
        $resource = fopen('php://temp', 'r+');
        fwrite($resource, 'test stream interface');
        rewind($resource);
        $stream = new Stream($resource);
        $result = $this->createStreamTraitImplementation->useCreateStreamTraitImplementation($stream);
        $this->assertInstanceOf(StreamInterface::class, $result);
        $this->assertEquals('test stream interface', (string) $result);
    }

    public function testCreateStreamWithInvalidType()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->createStreamTraitImplementation->useCreateStreamTraitImplementation(123);
    }
}