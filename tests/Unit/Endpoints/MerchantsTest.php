<?php

namespace Alma\API\Tests\Unit\Endpoints;

use Alma\API\ClientContext;
use Alma\API\Endpoints\Merchants;
use Alma\API\Entities\DTO\MerchantBusinessEvent\CartInitiatedBusinessEvent;
use Alma\API\Entities\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEvent;
use Alma\API\Exceptions\RequestException;
use Alma\API\Request;
use Alma\API\RequestError;
use Alma\API\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

class MerchantsTest extends TestCase
{

    /**
     * @var ClientContext
     */
    private $clientContext;
    /**
     * @var Merchants
     */
    private $merchantEndpoint;
    /**
     * @var Request
     */
    private $requestObject;
    /**
     * @var Response
     */
    private $responseMock;

    public function setUp(): void
    {
        $this->clientContext = Mockery::mock(ClientContext::class);
        $this->merchantEndpoint = Mockery::mock(Merchants::class)->makePartial();
        $loggerMock = Mockery::mock(LoggerInterface::class);
        $loggerMock->shouldReceive('error');
        $this->requestObject = Mockery::mock(Request::class);
        $this->responseMock = Mockery::mock(Response::class);
        $this->clientContext->logger = $loggerMock;
        $this->merchantEndpoint->setClientContext($this->clientContext);
    }

    public function tearDown(): void
    {
        $this->merchantEndpoint = null;
        $this->responseMock = null;
        $this->requestObject = null;
        $this->clientContext = null;
        Mockery::close();
    }

    public function testSendBusinessEventPostThrowRequestErrorThrowRequestException()
    {
        $this->merchantEndpoint->shouldReceive('request')
            ->with('/v1/me/business-events')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('setRequestBody')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('post')
            ->once()
            ->andThrow(new RequestError('Error in post', null, null));
        $this->expectException(RequestException::class);
        $this->merchantEndpoint->sendCartInitiatedBusinessEvent(new CartInitiatedBusinessEvent('42'));
    }

    public function testSendBusinessEventBadResponseRequestException()
    {
        $this->merchantEndpoint->shouldReceive('request')
            ->with('/v1/me/business-events')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('setRequestBody')
            ->once()
            ->andReturn($this->requestObject);
        $this->responseMock->errorMessage = 'Error in response';
        $this->responseMock->shouldReceive('isError')
            ->once()
            ->andReturn(true);
        $this->requestObject->shouldReceive('post')
            ->once()
            ->andReturn($this->responseMock);
        $this->expectException(RequestException::class);
        $this->merchantEndpoint->sendCartInitiatedBusinessEvent(new CartInitiatedBusinessEvent('42'));
    }

    public function testSendCartInitiatedEvent()
    {
        $cartInitiatedEvent = new CartInitiatedBusinessEvent('42');
        $this->merchantEndpoint->shouldReceive('request')
            ->with('/v1/me/business-events')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('setRequestBody')
            ->with(['event_type' => $cartInitiatedEvent->getEventType(), 'cart_id' => $cartInitiatedEvent->getCartId()])
            ->once()
            ->andReturn($this->requestObject);
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);
        $this->assertNull($this->merchantEndpoint->sendCartInitiatedBusinessEvent($cartInitiatedEvent));
    }

    public function testSendOrderConfirmedBusinessEventForNonAlmaPayment()
    {
        $orderConfirmedBusinessEvent = new OrderConfirmedBusinessEvent(
            false,
            false,
            true,
            '42',
            '54'
        );
        $this->merchantEndpoint->shouldReceive('request')
            ->with('/v1/me/business-events')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('setRequestBody')
            ->with(
                [
                    'event_type' => 'order_confirmed',
                    'is_alma_p1x' => false,
                    'is_alma_bnpl' => false,
                    'was_bnpl_eligible' => true,
                    'order_id' => '42',
                    'cart_id' => '54',
                    'alma_payment_id' => NULL
                ]
            )
            ->once()
            ->andReturn($this->requestObject);
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);
        $this->assertNull($this->merchantEndpoint->sendOrderConfirmedBusinessEvent($orderConfirmedBusinessEvent));
    }

    public function testSendOrderConfirmedBusinessEventForAlmaPayment()
    {
        $orderConfirmedBusinessEvent = new OrderConfirmedBusinessEvent(
            true,
            false,
            true,
            '42',
            '54',
            'alma_payment_id'
        );
        $this->merchantEndpoint->shouldReceive('request')
            ->with('/v1/me/business-events')
            ->once()
            ->andReturn($this->requestObject);
        $this->requestObject->shouldReceive('setRequestBody')
            ->with(
                [
                    'event_type' => 'order_confirmed',
                    'is_alma_p1x' => true,
                    'is_alma_bnpl' => false,
                    'was_bnpl_eligible' => true,
                    'order_id' => '42',
                    'cart_id' => '54',
                    'alma_payment_id' => 'alma_payment_id'
                ]
            )
            ->once()
            ->andReturn($this->requestObject);
        $this->responseMock->shouldReceive('isError')->once()->andReturn(false);
        $this->requestObject->shouldReceive('post')->once()->andReturn($this->responseMock);
        $this->assertNull($this->merchantEndpoint->sendOrderConfirmedBusinessEvent($orderConfirmedBusinessEvent));
    }
}