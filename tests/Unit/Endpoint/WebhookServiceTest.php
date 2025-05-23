<?php

namespace Alma\API\Tests\Unit\Endpoint;


use Alma\API\Endpoint\WebhookService;
use Alma\API\Entities\Webhook;
use Alma\API\Exceptions\ClientException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Exceptions\WebhookServiceException;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;

class WebhookServiceTest extends AbstractServiceSetUp
{
    const CREATE_WEBHOOK_RESPONSE_JSON = '{
        "id": "webhook_1213KqH5XRTfCB2w3ZK1qkZRahPy22Ex4J",
        "type": "ecommerce_report",
        "url": "https://api-sandbox.example-services.io"
    }';

    /** @var Response|Mock */
    protected $responseMock;

    /** @var Response|Mock */
    protected $badResponseMock;

    /** @var WebhookService|Mock */
    protected $webhookServiceMock;

    public function setUp(): void
    {
        parent::setUp();

        // Mocks
        $this->responseMock = Mockery::mock(Response::class);
        $this->responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->responseMock->shouldReceive('isError')->andReturn(false);
        $this->responseMock->shouldReceive('getBody')->andReturn(self::CREATE_WEBHOOK_RESPONSE_JSON);
        $this->responseMock->shouldReceive('getJson')->andReturn(json_decode(self::CREATE_WEBHOOK_RESPONSE_JSON, true));

        $this->badResponseMock = Mockery::mock(Response::class);
        $this->badResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $this->badResponseMock->shouldReceive('isError')->andReturn(true);
        $this->badResponseMock->shouldReceive('getBody')->andReturn(self::CREATE_WEBHOOK_RESPONSE_JSON);

        // WebhookService
        $this->webhookServiceMock = Mockery::mock(WebhookService::class, [$this->clientMock])->makePartial();
    }

    /**
     * Ensure we can create a webhook
     * @throws WebhookServiceException
     */
    public function testCreateWebhook(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // OrderService
        $result = $this->webhookServiceMock->create("ecommerce_report", "https://api-sandbox.example-services.io");

        // Assertions
        $this->assertInstanceOf(Webhook::class, $result);
    }

    /**
     * Ensure we can catch WebhookServiceException
     * @throws WebhookServiceException
     */
    public function testCreateWebhookWebhookServiceException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(WebhookServiceException::class);

        // Call
        $this->webhookServiceMock->create("ecommerce_report", "https://api-sandbox.example-services.io");
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws WebhookServiceException
     */
    public function testCreateWebhookRequestException(): void
    {
        // Mocks
        $webhookServiceMock = Mockery::mock(WebhookService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $webhookServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(WebhookServiceException::class);
        $webhookServiceMock->create("ecommerce_report", "https://api-sandbox.example-services.io");
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws WebhookServiceException
     */
    public function testCreateWebhookClientException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(WebhookServiceException::class);
        $this->webhookServiceMock->create("ecommerce_report", "https://api-sandbox.example-services.io");
    }

    /**
     * Ensure we can fetch a webhook
     * @throws WebhookServiceException
     */
    public function testFetchWebhook(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // OrderService
        $result = $this->webhookServiceMock->fetch("webhook_1213KqH5XRTfCB2w3ZK1qkZRahPy22Ex4J");

        // Assertions
        $this->assertInstanceOf(Webhook::class, $result);
    }

    /**
     * Ensure we can catch WebhookServiceException
     * @throws WebhookServiceException
     */
    public function testFetchWebhookWebhookServiceException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(WebhookServiceException::class);

        // Call
        $this->webhookServiceMock->fetch("webhook_1213KqH5XRTfCB2w3ZK1qkZRahPy22Ex4J");
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws WebhookServiceException
     */
    public function testFetchWebhookRequestException(): void
    {
        // Mocks
        $webhookServiceMock = Mockery::mock(WebhookService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $webhookServiceMock->shouldReceive('createGetRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(WebhookServiceException::class);
        $webhookServiceMock->fetch("webhook_1213KqH5XRTfCB2w3ZK1qkZRahPy22Ex4J");
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws WebhookServiceException
     */
    public function testFetchWebhookClientException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(WebhookServiceException::class);
        $this->webhookServiceMock->fetch("webhook_1213KqH5XRTfCB2w3ZK1qkZRahPy22Ex4J");
    }

    /**
     * Ensure we can delete a webhook
     * @throws WebhookServiceException
     */
    public function testDeleteWebhook(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // OrderService
        $result = $this->webhookServiceMock->delete("webhook_1213KqH5XRTfCB2w3ZK1qkZRahPy22Ex4J");

        // Assertions
        $this->assertTrue($result);
    }

    /**
     * Ensure we can catch WebhookServiceException
     * @throws WebhookServiceException
     */
    public function testDeleteWebhookWebhookServiceException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(WebhookServiceException::class);

        // Call
        $this->webhookServiceMock->delete("webhook_1213KqH5XRTfCB2w3ZK1qkZRahPy22Ex4J");
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws WebhookServiceException
     */
    public function testDeleteWebhookRequestException(): void
    {
        // Mocks
        $webhookServiceMock = Mockery::mock(WebhookService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $webhookServiceMock->shouldReceive('createDeleteRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(WebhookServiceException::class);
        $webhookServiceMock->delete("webhook_1213KqH5XRTfCB2w3ZK1qkZRahPy22Ex4J");
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws WebhookServiceException
     */
    public function testDeleteWebhookClientException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(WebhookServiceException::class);
        $this->webhookServiceMock->delete("webhook_1213KqH5XRTfCB2w3ZK1qkZRahPy22Ex4J");
    }
}