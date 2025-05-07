<?php
/**
 * Copyright (c) 2018 Alma / Nabla SAS
 *
 * THE MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and
 * to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @author    Alma / Nabla SAS <contact@getalma.eu>
 * @copyright Copyright (c) 2018 Alma / Nabla SAS
 * @license   https://opensource.org/licenses/MIT The MIT License
 *
 */

namespace Alma\API;

use Alma\API\Exceptions\RequestException;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Psr7\Stream;

class Request implements RequestInterface
{
    private string $method;
    private UriInterface $uri;
    private array $headers;
    private StreamInterface $body;
    private string $protocolVersion = '1.1';

    /**
     * @throws RequestException
     */
    public function __construct(string $method, $uri, array $headers = [], $body = null)
    {
        $this->method = strtoupper($method);
        $this->uri = ($uri instanceof UriInterface) ? $uri : new Uri($uri);
        $this->headers = $headers;
        $this->body = $this->createStream($body);
    }

    /**
     * @throws RequestException
     */
    private function createStream($body = null): StreamInterface
    {
        if (is_resource($body)) {
            $stream = new Stream($body);
        } elseif (is_string($body)) {
            $stream = fopen('php://temp', 'r+');
            if ($stream === false) {
                throw new RequestException('Failed to open temp stream');
            }
            fwrite($stream, $body);
            rewind($stream);
            $stream = new Stream($stream);
        } elseif ($body === null) {
            $stream =  new Stream(fopen('php://temp', 'r+')); // Retourne un stream vide
        } elseif ($body instanceof StreamInterface) {
            $stream =  $body;
        } else {
            throw new InvalidArgumentException('Invalid body type');
        }
        return $stream;
    }

    public function getRequestTarget(): string
    {
        $target = $this->uri->getPath();
        if ($this->uri->getQuery()) {
            $target .= '?' . $this->uri->getQuery();
        }
        return $target ?: '/';
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        // Gestion complexe, à adapter selon tes besoins
        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function withMethod(string $method): RequestInterface
    {
        $this->method = strtoupper($method);
        return $this;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    public function withUri(UriInterface $uri, bool $preserveHost = false): RequestInterface
    {
        $this->uri = $uri;
        return $this;
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): RequestInterface
    {
        $this->protocolVersion = $version;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function hasHeader(string $name): bool
    {
        $name = strtolower($name);
        return isset($this->headers[$name]);
    }

    public function getHeader(string $name): array
    {
        $name = strtolower($name);
        return $this->hasHeader($name) ? $this->headers[$name] : [];
    }

    public function getHeaderLine(string $name): string
    {
        return implode(', ', $this->getHeader($name));
    }

    public function withHeader(string $name, $value): RequestInterface
    {
        $name = strtolower($name);
        $this->headers[$name] = is_array($value) ? $value : [$value];
        return $this;
    }

    public function withAddedHeader(string $name, $value): RequestInterface
    {
        $name = strtolower($name);
        if (!$this->hasHeader($name)) {
            $this->headers[$name] = [];
        }
        $this->headers[$name] = array_merge($this->headers[$name], is_array($value) ? $value : [$value]);
        return $this;
    }

    public function withoutHeader(string $name): RequestInterface
    {
        $name = strtolower($name);
        unset($this->headers[$name]);
        return $this;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): RequestInterface
    {
        $this->body = $body;
        return $this;
    }
}
