<?php

namespace Alma\API\Tests\Unit;

use Alma\API\ClientConfiguration;
use Alma\API\CurlClient;
use Alma\API\Exceptions\ClientException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Request;
use Alma\API\Response;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\Uri;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;

class CurlClientTest extends TestCase
{
    /** @var CurlClient|Mock Default Client Mock  */
    private $clientMock;

    /** @var CurlClient|Mock Default Client Mock  */
    private $clientErrorMock;

    /** @var Request|Mock Default Client Mock  */
    private $requestMock;

    /** @var ClientConfiguration */
    private ClientConfiguration $clientConfiguration;

    private string $curlResponse = <<<EOF
HTTP/2 200
date: Thu, 22 May 2025 08:18:19 GMT
content-type: application/json
content-length: 806
set-cookie: alma_context=jEhJ5TfQua25b0-4z8Me0UhVzs6-J1uKQzWGsdv-SZk; Max-Age=63072000; Path=/; HttpOnly; Secure; Domain=.sandbox.getalma.eu; SameSite=None
cf-cache-status: DYNAMIC
set-cookie: xxx=yyy; path=/; expires=Thu, 22-May-25 08:48:19 GMT; domain=.getalma.eu; HttpOnly; Secure
server: cloudflare
cf-ray: 943ae1d8f811d646-CDG

<h1>This is my response</h1>
EOF;

    public function setUp(): void
    {
        parent::setUp();

        $this->clientConfiguration = new ClientConfiguration(
            'sk_test_xxxxxxxxxxxx'
        );

        // Mocks
        $this->requestMock = Mockery::mock(Request::class);
        $this->requestMock->shouldReceive('getUri')->andReturn(new Uri('https://api.getalma.eu'));
        $this->requestMock->shouldReceive('getMethod')->andReturn('GET');
        $this->requestMock->shouldReceive('getHeaders')->andReturn([]);
        $this->requestMock->shouldReceive('getBody')->andReturn($this->stringToStream('The content'));
        $this->clientMock = Mockery::mock(CurlClient::class, [$this->clientConfiguration]);
        $this->clientMock->makePartial();
        $this->clientMock->shouldReceive('init')->andReturn(true);
        $this->clientMock->shouldReceive('setOpt')->andReturn(true);

        $this->clientMock->shouldReceive('exec')->andReturn($this->curlResponse);
        $this->clientMock->shouldReceive('getInfo')->with(CURLINFO_HTTP_CODE)->andReturn(200);
        $this->clientMock->shouldReceive('getInfo')->with(CURLINFO_HEADER_SIZE)->andReturn(431);
        $this->clientMock->shouldReceive('getInfo')->with(CURLINFO_HTTP_VERSION)->andReturn('2.0');
        $this->clientMock->shouldReceive('getError')->andReturn(CURLE_OK);
        $this->clientMock->shouldReceive('getErrno')->andReturn(0);
        $this->clientMock->shouldReceive('close')->andReturn(true);

        // Error Mocks
        $this->clientErrorMock = Mockery::mock(CurlClient::class, [$this->clientConfiguration]);
        $this->clientErrorMock->makePartial();
        $this->clientErrorMock->shouldReceive('init')->andReturn(true);
        $this->clientErrorMock->shouldReceive('setOpt')->andReturn(true);
        $this->clientErrorMock->shouldReceive('exec')->andReturn('{"response": "ok"}');
        $this->clientErrorMock->shouldReceive('getInfo')->with(CURLINFO_HTTP_CODE)->andReturn(200);
        $this->clientErrorMock->shouldReceive('getInfo')->with(CURLINFO_HEADER_SIZE)->andReturn(431);
        $this->clientErrorMock->shouldReceive('getError')->andReturn(CURLE_COULDNT_CONNECT);
        $this->clientErrorMock->shouldReceive('getErrno')->andReturn(7);
        $this->clientErrorMock->shouldReceive('close')->andReturn(true);
    }

    public function stringToStream(string $string): Stream
    {
        $resourceFromString = fopen('php://temp', 'r+');
        fwrite($resourceFromString, $string);
        fseek($resourceFromString, 0);
        return new Stream($resourceFromString);
    }

    /**
     * @throws ClientException
     */
    public function testConfig()
    {
        $client = new CurlClient($this->clientConfiguration);
        $config = $client->getConfig();
        $this->assertEquals('https://api.getalma.eu', $config->getBaseUri());
        $this->assertEquals(30, $config->getTimeout());
        $this->assertEquals(['Content-Type' => ['application/json']], $config->getHeaders());
        $this->assertTrue($config->getSslVerify());
    }

    /**
     * Ensure we can do send Requests
     * @throws ClientException|RequestException
     */
    public function testSendRequest()
    {
        // Call
        $response = $this->clientMock->sendRequest($this->requestMock);

        // Assertions
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * Ensure we can do send Requests
     * @throws RequestException
     */
    public function testSendRequestWithError()
    {
        // Expectations
        $this->expectException(ClientException::class);

        // Call
        $response = $this->clientErrorMock->sendRequest($this->requestMock);
    }
}
