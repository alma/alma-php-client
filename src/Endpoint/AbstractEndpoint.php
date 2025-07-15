<?php

namespace Alma\API\Endpoint;

use Alma\API\CurlClient;
use Alma\API\Exceptions\RequestException;
use Alma\API\Request;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

abstract class AbstractEndpoint implements LoggerAwareInterface
{
    use loggerAwareTrait;

    /** @var CurlClient */
    protected CurlClient $client;

    public function __construct(CurlClient $client)
    {
        $this->client = $client;
        $this->logger = new NullLogger();
    }

    /**
     * @throws RequestException
     */
    private function createRequest(string $method, string $uri, array $body = []): Request {
        $headers = [
            'Authorization' => ['Alma-Auth ' . $this->client->getConfig()->getApiKey()]
        ];
        return new Request($method, $uri, $headers, json_encode($body));
    }

    /**
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
     * @throws RequestException
     */
    public function createPostRequest(string $uri, array $body = null): Request {
        return $this->createRequest('POST', $uri, $body);
    }

    /**
     * @throws RequestException
     */
    public function createPutRequest(string $uri, array $body = null): Request {
        return $this->createRequest('PUT', $uri, $body);
    }

    /**
     * @throws RequestException
     */
    public function createDeleteRequest(string $uri): Request {
        return $this->createRequest('DELETE', $uri);
    }
}
