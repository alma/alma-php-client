<?php

namespace Alma\API\Infrastructure;

use Alma\API\Infrastructure\Exception\RequestException;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

trait StreamTrait
{
    /**
     * @throws RequestException
     */
    protected function createStream($body = null): StreamInterface
    {
        return Utils::streamFor($body);

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
