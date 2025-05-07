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

use Alma\API\Exceptions\AlmaException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Client\ClientInterface;

class Client implements ClientInterface
{
    const VERSION = '3.0.0';

    private Configuration $config;

    public function __construct(array $config = [])
    {
        $this->config = new Configuration(
            array_merge([
                'base_uri' => '',
                'timeout'  => 30,
                'headers'  => [],
                'verify'   => true, // Check SSL certificate
            ], $config)
        );
    }

    public function getConfig(): Configuration
    {
        return $this->config;
    }

    /**
     * Sends an HTTP request and returns the response.
     *
     * @param RequestInterface $request The HTTP request to send.
     * @return ResponseInterface The HTTP response received.
     * @throws AlmaException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $url = $this->config->getBaseUri() . $request->getUri()->getPath();
        $headers = array_merge($this->config->getHeaders(), $request->getHeaders());

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, $this->config->getTimeout());
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getMethod());
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->formatHeaders($headers));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, $this->config->getSslVerify());
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, $this->config->getSslVerify() ? 2 : 0);

        $body = $request->getBody();
        curl_setopt($curl, CURLOPT_POSTFIELDS, (string) $body);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new AlmaException("cURL error: " . $error);
        }

        // Getting the HTTP status code and headers
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $responseHeaders = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headerString = substr($response, 0, $responseHeaders);
        $bodyContent = substr($response, $responseHeaders);

        curl_close($curl);

        // Return the response
        return new Response($statusCode, $this->parseHeaders($headerString), $bodyContent);
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
}
