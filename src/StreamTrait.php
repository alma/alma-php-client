<?php

namespace Alma\API;

use Alma\API\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

trait StreamTrait
{
    protected function createStream($body = null): StreamInterface
    {
        if (is_resource($body)) {
            $stream = new Stream($body);
        } elseif (is_string($body)) {
            $stream = fopen('php://temp', 'r+');
            if ($stream === false) {
                // @codeCoverageIgnoreStart
                throw new RequestException('Failed to open temp stream');
                // @codeCoverageIgnoreEnd
            }
            fwrite($stream, $body);
            rewind($stream);
            $stream = new Stream($stream);
        } elseif ($body === null) {
            $stream = new Stream(fopen('php://temp', 'r+'));
        } elseif ($body instanceof StreamInterface) {
            $stream = $body;
        } else {
            throw new InvalidArgumentException('Invalid body type');
        }
        return $stream;
    }
}