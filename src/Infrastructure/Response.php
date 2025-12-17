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

namespace Alma\API\Infrastructure;

use GuzzleHttp\Psr7\Utils;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    private int $statusCode;
    private array $headers;
    private StreamInterface $body;
    private string $protocolVersion = '1.1';
    private array $reasonPhrases = [
        100 => 'Continue',
        200 => 'OK',
        204 => 'No Content',
        302 => 'Found',
        400 => 'Bad Request',
        406 => 'Not Acceptable',
        500 => 'Internal Server Error',
    ];

    /**
     * Create a Response
     * @param int $statusCode
     * @param StreamInterface $body
     * @param array $headers
     * @param string $protocolVersion
     */
    public function __construct(int $statusCode, $body = '', array $headers = [], string $protocolVersion = '1.1')
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = Utils::streamFor($body);
        $this->protocolVersion = $this->validateProtocolVersion($protocolVersion);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function withStatus(int $code, string $reasonPhrase = ''): ResponseInterface
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getReasonPhrase(): string
    {
        return $this->reasonPhrases[$this->statusCode] ?? '';
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function withProtocolVersion(string $version): ResponseInterface
    {
        $this->protocolVersion = $this->validateProtocolVersion($version);
        return $this;
    }

    public function validateProtocolVersion(string $version): string
    {
        if (!in_array($version, ['1.0', '1.1', '2.0'])) {
            throw new InvalidArgumentException('Invalid HTTP protocol version: ' . $version);
        }
        return $version;
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

    public function withHeader(string $name, $value): ResponseInterface
    {
        $name = strtolower($name);
        $this->headers[$name] = is_array($value) ? $value : [$value];
        return $this;
    }

    public function withAddedHeader(string $name, $value): ResponseInterface
    {
        $name = strtolower($name);
        if (!$this->hasHeader($name)) {
            $this->headers[$name] = [];
        }
        $this->headers[$name] = array_merge($this->headers[$name], is_array($value) ? $value : [$value]);
        return $this;
    }

    public function withoutHeader(string $name): ResponseInterface
    {
        $name = strtolower($name);
        unset($this->headers[$name]);
        return $this;
    }

    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    public function withBody(StreamInterface $body): ResponseInterface
    {
        $this->body = $body;
        return $this;
    }

    public function getJson(): ?array
    {
        $data = json_decode($this->body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return $data;
    }

    public function getFile()
    {
        return $this->body;
    }

    public function isError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 600;
    }
}
