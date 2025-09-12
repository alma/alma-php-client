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

use Alma\API\Helper\StreamHelper;
use Alma\API\Infrastructure\Exception\ClientException;
use Alma\API\Infrastructure\Exception\DependenciesException;
use Alma\API\Infrastructure\Exception\RequestException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;

class CurlClient implements ClientInterface
{
    const VERSION = '3.0.0';

    private ClientConfiguration $config;

    /** @var resource|null */
    private $curlClient;

    /**
     * @throws ClientException
     */
    public function __construct(ClientConfiguration $config)
    {
        $this->config = $config;

        // @codeCoverageIgnoreStart
        try {
            $this->checkDependencies();
        } catch (DependenciesException $e) {
            throw new ClientException('Dependencies check failed: ' . $e->getMessage());
        }
        // @codeCoverageIgnoreEnd
    }

    public function getConfig(): ClientConfiguration
    {
        return $this->config;
    }

    /**
     * Sends an HTTP request and returns the response.
     *
     * @param RequestInterface $request The HTTP request to send.
     * @return ResponseInterface The HTTP response received.
     * @throws ClientException
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $url = $this->config->getBaseUri() . $request->getUri()->getPath();
        $query = $request->getUri()->getQuery();
        if (!empty($query)) {
            $url .= '?' . $query;
        }

        $headers = array_merge($this->config->getHeaders(), $request->getHeaders());

        $this->init($url);
        $this->setOpt(CURLOPT_RETURNTRANSFER, 1);
        $this->setOpt(CURLOPT_HEADER, 1);
        $this->setOpt(CURLOPT_HTTP_VERSION, $this->config->getProtocolVersion());
        $this->setOpt(CURLOPT_TIMEOUT, $this->config->getTimeout());
        $this->setOpt(CURLOPT_CUSTOMREQUEST, $request->getMethod());
        $this->setOpt(CURLOPT_HTTPHEADER, $this->formatHeaders($headers));
        $this->setOpt(CURLOPT_SSL_VERIFYPEER, $this->config->getSslVerify());
        $this->setOpt(CURLOPT_SSL_VERIFYHOST, $this->config->getSslVerify() ? 2 : 0);

        $body = $request->getBody();
        $this->setOpt(CURLOPT_POSTFIELDS, (string) $body);

        $response = $this->exec();

        if ($this->getErrno()) {
            $error = $this->getError();
            $this->close();
            throw new ClientException("cURL error: " . $error);
        }

        // Getting the HTTP status code and headers
        $statusCode = $this->getInfo( CURLINFO_HTTP_CODE);
        $responseHeaders = $this->getInfo( CURLINFO_HEADER_SIZE);
        $httpVersion = $this->getInfo(CURLINFO_HTTP_VERSION);
        $headerString = substr($response, 0, $responseHeaders);
        $bodyContent = substr($response, $responseHeaders);
        $this->close();

        // Return the response
        return new Response($statusCode, $bodyContent, $this->parseHeaders($headerString), $httpVersion);
    }

    private function formatHeaders(array $headers): array
    {
        $formattedHeaders = [];
        foreach ($headers as $name => $values) {
            foreach ($values as $value) {
                $formattedHeaders[] = $name . ': ' . $value;
            }
        }
        return $formattedHeaders;
    }

    private function parseHeaders(string $headerString): array
    {
        $headers = [];
        $lines = explode("\r\n", trim($headerString));
        foreach ($lines as $line) {
            if (strpos($line, ':') !== false) {
                list($name, $value) = explode(':', $line, 2);
                $name = trim($name);
                $value = trim($value);
                $headers[$name][] = $value;
            }
        }
        return $headers;
    }

    /** @codeCoverageIgnore Curl Wrapper used to test sendRequest function */
    public function init(string $url = null): bool
    {
        $this->curlClient = curl_init($url);
        return $this->curlClient !== false;
    }

    /** @codeCoverageIgnore Curl Wrapper used to test sendRequest function */
    public function setOpt(int $option, $value): bool
    {
        return curl_setopt($this->curlClient, $option, $value);
    }

    /** @codeCoverageIgnore Curl Wrapper used to test sendRequest function */
    public function exec()
    {
        return curl_exec($this->curlClient);
    }

    /** @codeCoverageIgnore Curl Wrapper used to test sendRequest function */
    public function getInfo(int $option = 0)
    {
        return curl_getinfo($this->curlClient, $option);
    }

    /** @codeCoverageIgnore Curl Wrapper used to test sendRequest function */
    public function getError(): int
    {
        return curl_error($this->curlClient);
    }

    /** @codeCoverageIgnore Curl Wrapper used to test sendRequest function */
    public function getErrno(): int
    {
        return curl_errno($this->curlClient);
    }

    /** @codeCoverageIgnore Curl Wrapper used to test sendRequest function */
    public function close(): void
    {
        curl_close($this->curlClient);
    }

    /**
     * @codeCoverageIgnore Can't mock dependencies
     * @throws DependenciesException
     */
    private function checkDependencies()
    {
        if (!function_exists('curl_init')) {
            throw new DependenciesException('Alma requires the CURL PHP extension.');
        }

        if (!function_exists('json_decode')) {
            throw new DependenciesException('Alma requires the JSON PHP extension.');
        }

        if (!defined('OPENSSL_VERSION_TEXT')) {
            throw new DependenciesException('Alma requires OpenSSL >= 1.1.1');
        }

        preg_match('/^(?:Libre|Open)SSL ([\d.]+)/', OPENSSL_VERSION_TEXT, $matches);
        if (empty($matches[1])) {
            throw new DependenciesException('Alma requires OpenSSL >= 1.1.1');
        }

        if (!version_compare($matches[1], '1.1.1', '>=')) {
            throw new DependenciesException('Alma requires OpenSSL >= 1.1.1');
        }
    }
}
