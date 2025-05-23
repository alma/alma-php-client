<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Endpoint\ShareOfCheckoutService;
use Alma\API\Exceptions\ClientException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Exceptions\ShareOfCheckoutServiceException;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;

class ShareOfCheckoutServiceTest extends AbstractServiceSetUp
{
    const SHARE_OF_CHECKOUT_RESPONSE_JSON = '{
            "merchant_id": "merchant_11xYpTY1GTkww5uWFKFdOllK82S1r7j5v5",
            "start_time": 1739343778,
            "end_time": 1739343778
        }';

    /** @var ShareOfCheckoutService|Mock */
    protected $shareOfCheckoutServiceMock;

    /** @var Response|Mock */
    protected $responseMock;

    /** @var Response|Mock */
    protected $badResponseMock;

    public function setUp(): void
    {
        parent::setUp();

        // Mocks
        $this->responseMock = Mockery::mock(Response::class);
        $this->responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->responseMock->shouldReceive('isError')->andReturn(false);
        $this->responseMock->shouldReceive('getBody')->andReturn(self::SHARE_OF_CHECKOUT_RESPONSE_JSON);
        $this->responseMock->shouldReceive('getJson')->andReturn(json_decode(self::SHARE_OF_CHECKOUT_RESPONSE_JSON, true));

        $this->badResponseMock = Mockery::mock(Response::class);
        $this->badResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $this->badResponseMock->shouldReceive('isError')->andReturn(true);
        $this->badResponseMock->shouldReceive('getBody')->andReturn(self::SHARE_OF_CHECKOUT_RESPONSE_JSON);

        // ShareOfCheckoutService
        $this->shareOfCheckoutServiceMock = Mockery::mock(ShareOfCheckoutService::class, [$this->clientMock])
            ->makePartial();
    }

    /**
     * Ensure we can send the share of checkout
     * @throws ShareOfCheckoutServiceException
     */
    public function testShare(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // Call
        $result = $this->shareOfCheckoutServiceMock->share(['data' => 'test']);

        // Assertions
        $this->assertEquals(json_decode(self::SHARE_OF_CHECKOUT_RESPONSE_JSON, true), $result);
    }

    /**
     * Ensure we can catch ShareOfCheckoutServiceException
     * @throws ShareOfCheckoutServiceException
     */
    public function testShareShareOfCheckoutServiceException(): void
    {
        // Mocks

        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $this->shareOfCheckoutServiceMock->share(['data' => 'test']);
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws ShareOfCheckoutServiceException
     */
    public function testShareRequestException(): void
    {
        // Mocks
        $shareOfCheckoutServiceMock = Mockery::mock(ShareOfCheckoutService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $shareOfCheckoutServiceMock->shouldReceive('createPutRequest')->andThrow(new RequestException("request error"));

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $shareOfCheckoutServiceMock->share(['data' => 'test']);
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws ShareOfCheckoutServiceException
     */
    public function testShareClientException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $this->shareOfCheckoutServiceMock->share(['data' => 'test']);
    }

    /**
     * Ensure we can get the latest share of checkout
     * @throws ShareOfCheckoutServiceException
     */
    public function testGetLastUpdateDates(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // Call
        $result = $this->shareOfCheckoutServiceMock->getLastUpdateDates();

        // Assertions
        $this->assertEquals(json_decode(self::SHARE_OF_CHECKOUT_RESPONSE_JSON, true), $result);
    }

    /**
     * Ensure we can catch ShareOfCheckoutServiceException
     * @throws ShareOfCheckoutServiceException
     */
    public function testGetLastUpdateDatesShareOfCheckoutServiceException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $this->shareOfCheckoutServiceMock->getLastUpdateDates();
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws ShareOfCheckoutServiceException
     */
    public function testGetLastUpdateDatesRequestException(): void
    {
        // Mocks
        $shareOfCheckoutServiceMock = Mockery::mock(ShareOfCheckoutService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $shareOfCheckoutServiceMock->shouldReceive('createGetRequest')->andThrow(new RequestException("request error"));

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $shareOfCheckoutServiceMock->getLastUpdateDates();
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws ShareOfCheckoutServiceException
     */
    public function testGetLastUpdateDatesClientException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $this->shareOfCheckoutServiceMock->getLastUpdateDates();
    }

    /**
     * Ensure we can add consent to share of checkout
     * @throws ShareOfCheckoutServiceException
     */
    public function testAddConsent(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // Assertions
        $this->assertTrue($this->shareOfCheckoutServiceMock->addConsent());
    }

    /**
     * Ensure we can catch ShareOfCheckoutServiceException
     * @throws ShareOfCheckoutServiceException
     */
    public function testAddConsentShareOfCheckoutServiceException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $this->shareOfCheckoutServiceMock->addConsent();
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws ShareOfCheckoutServiceException
     */
    public function testAddConsentRequestException(): void
    {
        // Mocks
        $shareOfCheckoutServiceMock = Mockery::mock(ShareOfCheckoutService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $shareOfCheckoutServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $shareOfCheckoutServiceMock->addConsent();
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws ShareOfCheckoutServiceException
     */
    public function testAddConsentClientException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $this->shareOfCheckoutServiceMock->addConsent();
    }

    /**
     * Ensure we can remove consent to share of checkout
     * @throws ShareOfCheckoutServiceException
     */
    public function testRemoveConsent(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // Assertions
        $this->assertTrue($this->shareOfCheckoutServiceMock->removeConsent());
    }

    /**
     * Ensure we can catch ShareOfCheckoutServiceException
     * @throws ShareOfCheckoutServiceException
     */
    public function testRemoveConsentShareOfCheckoutServiceException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badResponseMock);

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $this->shareOfCheckoutServiceMock->removeConsent();
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws ShareOfCheckoutServiceException
     */
    public function testRemoveConsentRequestException(): void
    {
        // Mocks
        $shareOfCheckoutServiceMock = Mockery::mock(ShareOfCheckoutService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $shareOfCheckoutServiceMock->shouldReceive('createDeleteRequest')->andThrow(new RequestException("request error"));

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $shareOfCheckoutServiceMock->removeConsent();
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws ShareOfCheckoutServiceException
     */
    public function testRemoveConsentClientException(): void
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Expectations
        $this->expectException(ShareOfCheckoutServiceException::class);

        // Call
        $this->shareOfCheckoutServiceMock->removeConsent();
    }

}

