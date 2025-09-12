<?php

namespace Alma\API\Infrastructure\Endpoint;

use Alma\API\Infrastructure\CurlClient;
use Alma\API\Infrastructure\Exception\RequestException;
use Alma\API\Infrastructure\Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

abstract class AbstractEndpoint implements LoggerAwareInterface
{
    use loggerAwareTrait;

    /** @var CurlClient */
    protected CurlClient $client;

    /**
     * Init the Endpoint
     * @param CurlClient $client The Client to use with the Endpoint
     */
    public function __construct(CurlClient $client)
    {
        $this->client = $client;
        $this->logger = new NullLogger();
    }

    /**
     * Create a Request
     * @param string $method The HTTP verb of the Request
     * @param string $uri The endpoint URI
     * @param array $body The body of the Request
     * @return Request The Request object
     * @throws RequestException
     */
    private function createRequest(string $method, string $uri, array $body = []): Request {
        $headers = [
            'Authorization' => ['Alma-Auth ' . $this->client->getConfig()->getApiKey()]
        ];
        return new Request($method, $uri, $headers, json_encode($body));
    }

    /**
     * Create a GET Request
     *
     * @param string $uri The endpoint URI
     * @param array $queryParams The query parameters
     * @return Request The Request object
     * @throws RequestException
     */
    public function createGetRequest(string $uri, array $queryParams = []): Request {
        $queryString = http_build_query($queryParams);
        if ($queryString) {
            $uri .= '?' . $queryString;
        }
        return $this->createRequest('GET', $uri);
    }

    /**
     * Create a POST Request
     *
     * @param string $uri The endpoint URI
     * @param array $body The body attributes
     * @return Request The Request object
     * @throws RequestException
     */
    public function createPostRequest(string $uri, array $body = []): Request {
        return $this->createRequest('POST', $uri, $body);
    }

    /**
     * Create a PUT Request
     *
     * @param string $uri The endpoint URI
     * @param array $body The body attributes
     * @return Request The Request object
     * @throws RequestException
     */
    public function createPutRequest(string $uri, array $body = []): Request {
        return $this->createRequest('PUT', $uri, $body);
    }

    /**
     * Create de DELETE Request
     *
     * @param string $uri The endpoint URI
     * @return Request The Request object
     * @throws RequestException
     */
    public function createDeleteRequest(string $uri): Request {
        return $this->createRequest('DELETE', $uri);
    }
}
