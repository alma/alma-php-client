<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\DTO\CustomerDto;
use Alma\API\DTO\OrderDto;
use Alma\API\DTO\PaymentDto;
use Alma\API\DTO\RefundDto;
use Alma\API\Endpoint\PaymentEndpoint;
use Alma\API\Entity\Order;
use Alma\API\Entity\Payment;
use Alma\API\Exception\ClientException;
use Alma\API\Exception\Endpoint\PaymentEndpointException;
use Alma\API\Exception\ParametersException;
use Alma\API\Exception\RequestException;
use Alma\API\Response;
use Mockery;
use Mockery\Mock;

/**
 * Class Payments
 */
class PaymentEndpointTest extends AbstractEndpointSetUp
{
    const MERCHANT_REF = "merchant_ref";

    /** @var string Classical JSON response */
    const SERVER_REQUEST_RESPONSE_JSON = '{
       "amount_already_refunded":1200,
       "custom_data":{},
       "customer_fee":100,
       "customer_interest":0,
       "deferred_days":0,
       "deferred_months":0,
       "expired_at":1657528851,
       "id":"payment_xxxxxx",
       "installments_count":3,
       "kind":"installments",         
       "payment_plan":[
          {
             "customer_fee":100,
             "customer_interest":0,
             "date_paid":1649672472,
             "due_date":1649672451,
             "id":"installment_11uPRjPgPJ5jxmEEXxIPPOvVA3Fh7cg13m",
             "original_purchase_amount":11733,
             "purchase_amount":11733,
             "state":"paid"
          },
          {
             "customer_fee":0,
             "customer_interest":0,
             "date_paid":null,
             "due_date":1652264451,
             "id":"installment_11uPRjP4a3cFQvoM1lehDnpxa2tYm2Hy0I",
             "original_purchase_amount":11733,
             "purchase_amount":0,
             "state":"paid"
          },
          {
             "customer_fee":0,
             "customer_interest":0,
             "date_paid":null,
             "due_date":1654942851,
             "id":"installment_11uPRjP79fG5QtHO54lSYsnDvyHb56oeCn",
             "original_purchase_amount":11733,
             "purchase_amount":0,
             "state":"paid"
          }
       ],
       "purchase_amount":11733,
       "state":"paid",
       "url":"https://test.alma.eu/payments/v2/payments/payment_xxxxxx"
    }';

    /** @var string JSON response for add order */
    const SERVER_REQUEST_ORDER_RESPONSE_JSON = '[
        {
            "comment": "comment",
            "created": 1747829359,
            "customer_url": "customer_url",
            "data": {},
            "id": "order_1213IpL5UdjoOqjgdljmNPMB3MOJw5vtgd",
            "merchant_reference": "C1-000027951",
            "merchant_url": "merchant_url",
            "payment": "payment_1213Ioh6xMAUYk7OTZ52VHNgGGwgT9B4ro",
            "updated": 1747829359
        }
    ]';

    /** @var Response|Mock */
    protected $paymentResponseMock;

    /** @var Response|Mock */
    protected $badPaymentResponseMock;

    /** @var Response|Mock */
    protected $orderResponseMock;

    /** @var Response|Mock */
    protected $badOrderResponseMock;

    /** @var PaymentEndpoint */
    protected PaymentEndpoint $paymentService;

    /**
     * Set up the paymentService
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Mocks
        $this->paymentResponseMock = Mockery::mock(Response::class);
        $this->paymentResponseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->paymentResponseMock->shouldReceive('isError')->andReturn(false);
        $this->paymentResponseMock->shouldReceive('getBody')->andReturn(self::SERVER_REQUEST_RESPONSE_JSON);
        $this->paymentResponseMock->shouldReceive('getJson')->andReturn(json_decode(self::SERVER_REQUEST_RESPONSE_JSON, true));

        $this->badPaymentResponseMock = Mockery::mock(Response::class);
        $this->badPaymentResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badPaymentResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $this->badPaymentResponseMock->shouldReceive('isError')->andReturn(true);
        $this->badPaymentResponseMock->shouldReceive('getBody')->andReturn(self::SERVER_REQUEST_RESPONSE_JSON);

        $this->orderResponseMock = Mockery::mock(Response::class);
        $this->orderResponseMock->shouldReceive('getStatusCode')->andReturn(200);
        $this->orderResponseMock->shouldReceive('isError')->andReturn(false);
        $this->orderResponseMock->shouldReceive('getBody')->andReturn(self::SERVER_REQUEST_ORDER_RESPONSE_JSON);
        $this->orderResponseMock->shouldReceive('getJson')->andReturn(json_decode(self::SERVER_REQUEST_ORDER_RESPONSE_JSON, true));

        $this->badOrderResponseMock = Mockery::mock(Response::class);
        $this->badOrderResponseMock->shouldReceive('getStatusCode')->andReturn(500);
        $this->badOrderResponseMock->shouldReceive('getReasonPhrase')->andReturn('Internal Server Error');
        $this->badOrderResponseMock->shouldReceive('isError')->andReturn(true);
        $this->badOrderResponseMock->shouldReceive('getBody')->andReturn(self::SERVER_REQUEST_ORDER_RESPONSE_JSON);

        $this->paymentService = new PaymentEndpoint($this->clientMock);
    }


    /**
     * Ensure payment creation is ok
     * @return void
     * @throws PaymentEndpointException
     */
    public function testCreatePayment()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->paymentResponseMock);

        // Assertions
        $this->assertInstanceOf(
            Payment::class,
            $this->paymentService->create(
                new PaymentDto(1000),
                new OrderDto(),
                new CustomerDto()
            )
        );
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testCreatePaymentRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentEndpointException::class);
        $paymentServiceMock->create(
            new PaymentDto(1000),
            new OrderDto(),
            new CustomerDto()
        );
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testCreatePaymentClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->create(
            new PaymentDto(1000),
            new OrderDto(),
            new CustomerDto()
        );
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentEndpointException
     */
    public function testCreatePaymentPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->create(
            new PaymentDto(1000),
            new OrderDto(),
            new CustomerDto()
        );
    }

    /**
     * Ensure payment Cancellation is ok
     * @return void
     * @throws PaymentEndpointException
     */
    public function testCancelPayment()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->paymentResponseMock);

        // Assertions
        $this->assertTrue($this->paymentService->cancel('id_1234'));
    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testCancelPaymentRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPutRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentEndpointException::class);
        $paymentServiceMock->cancel('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testCancelPaymentClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->cancel('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentEndpointException
     */
    public function testCancelPaymentPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->cancel('id_1234');
    }

    /**
     * Ensure payment fetching is ok
     * @return void
     * @throws PaymentEndpointException
     */
    public function testFetchPayment()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->paymentResponseMock);

        // Assertions
        $this->assertInstanceOf(Payment::class, $this->paymentService->fetch('id_1234'));

    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testFetchPaymentRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createGetRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentEndpointException::class);
        $paymentServiceMock->fetch('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testFetchPaymentClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->fetch('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentEndpointException
     */
    public function testFetchPaymentPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->fetch('id_1234');
    }

    /**
     * Ensure payment edition is ok
     * @return void
     * @throws PaymentEndpointException
     */
    public function testEditPayment()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->paymentResponseMock);

        // Assertions
        $this->assertInstanceOf(Payment::class, $this->paymentService->edit('id_1234'));

    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testEditPaymentRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentEndpointException::class);
        $paymentServiceMock->edit('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testEditPaymentClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->edit('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentEndpointException
     */
    public function testEditPaymentPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->edit('id_1234');
    }

    /**
     * Ensure that flag a potential fraud is ok
     * @return void
     * @throws PaymentEndpointException
     */
    public function testFlagAsPotentialFraud()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->paymentResponseMock);

        // Assertions
        $this->assertTrue($this->paymentService->flagAsPotentialFraud('id_1234'));
        $this->assertTrue($this->paymentService->flagAsPotentialFraud('id_1234', 'reason'));

    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testFlagAsPotentialFraudRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentEndpointException::class);
        $paymentServiceMock->flagAsPotentialFraud('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testFlagAsPotentialFraudClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->flagAsPotentialFraud('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentEndpointException
     */
    public function testFlagAsPotentialFraudPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->flagAsPotentialFraud('id_1234');
    }

    // TODO REFUND TEST

    public function testPartialRefund()
    {
        $paymentId = 'payment_1234';
        $amount = 1000; // Amount to refund in cents
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->paymentResponseMock);
        $refundDto = (new RefundDto())->setAmount($amount);

        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')
            ->with(
                '/v1/payments/payment_1234/refund',
                ['amount' => 1000]
            )
            ->once();

        // Act
        $result = $paymentServiceMock->refund($paymentId, $refundDto);

        // Assert
        $this->assertInstanceOf(Payment::class, $result);
    }

    /**
     * Ensure payment trigger is ok
     * @return void
     * @throws PaymentEndpointException
     */
    public function testTriggerPayment()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->paymentResponseMock);

        // Assertions
        $this->assertInstanceOf(Payment::class, $this->paymentService->trigger('id_1234'));

    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testTriggerPaymentRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentEndpointException::class);
        $paymentServiceMock->trigger('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testTriggerPaymentClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->trigger('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentEndpointException
     */
    public function testTriggerPaymentPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->trigger('id_1234');
    }

    /**
     * Ensure payment edition is ok
     * @return void
     * @throws PaymentEndpointException
     */
    public function testAddOrder()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->orderResponseMock);

        // Assertions
        $this->assertInstanceOf(Order::class, $this->paymentService->addOrder('id_1234'));
        $this->assertInstanceOf(Order::class, $this->paymentService->addOrder('id_1234', [], true));

    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testAddOrderRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPutRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentEndpointException::class);
        $paymentServiceMock->addOrder('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testAddOrderClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->addOrder('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentEndpointException
     */
    public function testAddOrderPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badOrderResponseMock);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->addOrder('id_1234');
    }

    /**
     * Ensure order overwrite is ok
     * @return void
     * @throws PaymentEndpointException
     */
    public function testOverwriteOrder()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->orderResponseMock);

        // Assertions
        $this->assertInstanceOf(Order::class, $this->paymentService->overwriteOrder('id_1234'));

    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testOverwriteOrderRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentEndpointException::class);
        $paymentServiceMock->overwriteOrder('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testOverwriteOrderClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->overwriteOrder('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentEndpointException
     */
    public function testOverwriteOrderPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badOrderResponseMock);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->overwriteOrder('id_1234');
    }


    /**
     * Ensure payment edition is ok
     * @return void
     * @throws PaymentEndpointException
     */
    public function testAddOrderStatusByMerchantOrderReference()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->orderResponseMock);

        // Assertions
        $this->assertTrue($this->paymentService->addOrderStatusByMerchantOrderReference(
            'payment_id',
            'merchant_order_reference',
            'status'
        ));

    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testAddOrderStatusByMerchantOrderReferenceRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentEndpointException::class);
        $paymentServiceMock->addOrderStatusByMerchantOrderReference(
            'payment_id',
            'merchant_order_reference',
            'status'
        );
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testAddOrderStatusByMerchantOrderReferenceClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->addOrderStatusByMerchantOrderReference(
            'payment_id',
            'merchant_order_reference',
            'status'
        );
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentEndpointException
     */
    public function testAddOrderStatusByMerchantOrderReferencePaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->addOrderStatusByMerchantOrderReference(
            'payment_id',
            'merchant_order_reference',
            'status'
        );
    }

    /**
     * Ensure payment edition is ok
     * @return void
     * @throws PaymentEndpointException
     */
    public function testSendSms()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->paymentResponseMock);

        // Assertions
        $this->assertTrue($this->paymentService->sendSms('id_1234'));

    }

    /**
     * Ensure we can catch RequestException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testSendSmsRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentEndpointException::class);
        $paymentServiceMock->sendSms('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentEndpointException
     */
    public function testSendSmsClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->sendSms('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentEndpointException
     */
    public function testSensSmsPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentEndpointException::class);
        $this->paymentService->sendSms('id_1234');
    }
}
