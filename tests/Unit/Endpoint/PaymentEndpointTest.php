<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Entities\DTO\CustomerDto;
use Alma\API\Entities\DTO\OrderDto;
use Alma\API\Entities\DTO\PaymentDto;
use Alma\API\Entities\Order;
use Alma\API\Entities\Payment;
use Alma\API\Exceptions\ClientException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\PaymentServiceException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Response;
use Mockery;
use Alma\API\Endpoint\PaymentEndpoint;
use Mockery\Mock;

/**
 * Class Payments
 */
class PaymentEndpointTest extends AbstractEndpointSetUp
{
    const MERCHANT_REF = "merchant_ref";

    /** @var string Classical JSON response*/
    const SERVER_REQUEST_RESPONSE_JSON = '{
       "payment_plan":[
          {
             "customer_can_postpone":false,
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
             "customer_can_postpone":false,
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
             "customer_can_postpone":false,
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
       "orders":[
          {
             "carrier":"carrier",
             "tracking_number":123,
             "tracking_url":"url",
             "comment":"comment",
             "created":1649672451,
             "customer_url":"customer_url",
             "data":{},
             "id":"order_11uPRjP4L9Dgbttx3cFUKGFPppdZIlrR2V",
             "merchant_reference":"00000206",
             "merchant_url":"merchant_url",
             "payment":"payment_11uPRjP5FaIYygQao8WdMQFnKRMOV14frx"
          }
       ],
       "refunds":[
          {
             "amount":35299,
             "created":1649770695,
             "from_payment_cancellation":false,
             "id":"refund_11uPrHz4TfHmQrD1OkYyWb4hPuq3673Vqm",
             "merchant_reference":null,
             "payment":"payment_11uPRjP5FaIYygQao8WdMQFnKRMOV14frx",
             "payment_refund_amount":11833,
             "rebate_amount":23466
          }
       ]
    }';

    /** @var string JSON response for add order*/
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
     * @throws PaymentServiceException
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
     * @throws PaymentServiceException
     */
    public function testCreatePaymentRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentServiceException::class);
        $paymentServiceMock->create(
            new PaymentDto(1000),
            new OrderDto(),
            new CustomerDto()
        );
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentServiceException
     */
    public function testCreatePaymentClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->create(
            new PaymentDto(1000),
            new OrderDto(),
            new CustomerDto()
        );
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentServiceException
     */
    public function testCreatePaymentPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->create(
            new PaymentDto(1000),
            new OrderDto(),
            new CustomerDto()
        );
    }

    /**
     * Ensure payment Cancellation is ok
     * @return void
     * @throws PaymentServiceException
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
     * @throws PaymentServiceException
     */
    public function testCancelPaymentRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPutRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentServiceException::class);
        $paymentServiceMock->cancel('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentServiceException
     */
    public function testCancelPaymentClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->cancel('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentServiceException
     */
    public function testCancelPaymentPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->cancel('id_1234');
    }

    /**
     * Ensure payment fetching is ok
     * @return void
     * @throws PaymentServiceException
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
     * @throws PaymentServiceException
     */
    public function testFetchPaymentRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createGetRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentServiceException::class);
        $paymentServiceMock->fetch('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentServiceException
     */
    public function testFetchPaymentClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->fetch('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentServiceException
     */
    public function testFetchPaymentPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->fetch('id_1234');
    }

    /**
     * Ensure payment edition is ok
     * @return void
     * @throws PaymentServiceException
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
     * @throws PaymentServiceException
     */
    public function testEditPaymentRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentServiceException::class);
        $paymentServiceMock->edit('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentServiceException
     */
    public function testEditPaymentClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->edit('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentServiceException
     */
    public function testEditPaymentPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->edit('id_1234');
    }

    /**
     * Ensure that flag a potential fraud is ok
     * @return void
     * @throws PaymentServiceException
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
     * @throws PaymentServiceException
     */
    public function testFlagAsPotentialFraudRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentServiceException::class);
        $paymentServiceMock->flagAsPotentialFraud('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentServiceException
     */
    public function testFlagAsPotentialFraudClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->flagAsPotentialFraud('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentServiceException
     */
    public function testFlagAsPotentialFraudPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->flagAsPotentialFraud('id_1234');
    }

    /**
     * Return input to test testPartialRefund
     * @return array[]
     */
    public static function partialRefundProvider(): array
    {
        return [
            [[
                'id' => "some_id",
                'amount' => 15000,
                'merchant_ref' => self::MERCHANT_REF,
                'comment' => "some comment"
            ]],
            [[
                'id' => "some_id",
                'amount' => 15000,
                'merchant_ref' => self::MERCHANT_REF
            ]],
            [[
                'id' => "some_id",
                'amount' => 15000
            ]]
        ];
    }

    /**
     * Test the partialRefund method with valid data
     * @dataProvider partialRefundProvider
     * @param $data
     * @return void
     * @throws PaymentServiceException|ParametersException
     */
    public function testPartialRefund($data)
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')
            ->once()
            ->andReturn($this->paymentResponseMock);
        $paymentService = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        // Call
        $this->callPartialRefund($paymentService, $data);
    }

    /**
     * Return invalid input to test testPartialRefund
     * @return array[]
     */
    public static function partialRefundInvalidProvider(): array
    {
        return [
            [[
                'id' => "zero_amount",
                'amount' => 0
            ], ParametersException::class],
            [[
                'id' => "negative_amount",
                'amount' => -1
            ], ParametersException::class],
            [[
                'id' => "",
                'amount' => 1500,
                'merchant_ref' => "no id",
            ], ParametersException::class],
        ];
    }

    /**
     * Test the partialRefund method with valid data
     * @dataProvider partialRefundInvalidProvider
     * @param $data
     * @param $expectedException
     * @return void
     * @throws PaymentServiceException|ParametersException
     */
    public function testInvalidPartialRefund($data, $expectedException)
    {
        // Mocks
        $paymentService = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        // Expectations
        $this->expectException($expectedException);

        // Call
        $this->callPartialRefund($paymentService, $data);
    }

    /**
     * Ensure we can do partial refunds
     * @param PaymentEndpoint $paymentService
     * @param $data
     * @return void
     * @throws PaymentServiceException|ParametersException
     */
    private function callPartialRefund(PaymentEndpoint $paymentService, $data)
    {
        if (isset($data['merchant_ref']) && isset($data['comment'])) {
            $paymentService->partialRefund($data['id'], $data['amount'], $data['merchant_ref'], $data['comment']);
        } elseif (isset($data['merchant_ref'])) {
            $paymentService->partialRefund($data['id'], $data['amount'], $data['merchant_ref']);
        } elseif (isset($data['comment'])) {
            $paymentService->partialRefund($data['id'], $data['amount'], '', $data['comment']);
        } else {
            $paymentService->partialRefund($data['id'], $data['amount']);
        }
    }

    /**
     * Return input to test testFullRefund
     * @return array[]
     */
    public static function fullRefundProvider(): array
    {
        return [
            [[
                'id' => "some_id",
                'merchant_ref' => self::MERCHANT_REF,
                'comment' => "some comment"
            ]],
            [[
                'id' => "some_id",
                'merchant_ref' => self::MERCHANT_REF
            ]],
            [[
                'id' => "some_id",
            ]]
        ];
    }

    /**
     * Test the fullRefund method with valid data
     * @dataProvider fullRefundProvider
     * @return void
     * @throws PaymentServiceException|ParametersException
     */
    public function testFullRefund($data)
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')
            ->once()
            ->andReturn($this->paymentResponseMock);

        // PaymentService
        $paymentService = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        // Call
        $payment = $this->callFullRefund($paymentService, $data);

        // Assertions
        /** @var Mixed|Payment $payment */
        $this->assertInstanceOf(Payment::class, $payment);
    }

    /**
     * @throws PaymentServiceException|ParametersException
     */
    private function callFullRefund(PaymentEndpoint $paymentService, $data): Payment
    {
        if (isset($data['merchant_ref']) && isset($data['comment'])) {
            return $paymentService->fullRefund($data['id'], $data['merchant_ref'], $data['comment']);
        } elseif (isset($data['merchant_ref'])) {
            return $paymentService->fullRefund($data['id'], $data['merchant_ref']);
        } elseif (isset($data['comment'])) {
            return $paymentService->fullRefund($data['id'], '', $data['comment']);
        } else {
            return $paymentService->fullRefund($data['id']);
        }
    }

    /**
     * Return invalid input to test testFullRefund
     * @return array[]
     */
    public static function fullRefundInvalidProvider(): array
    {
        return [
            [[
                'id' => "",
                'merchant_ref' => "no id",
            ], ParametersException::class],
        ];
    }

    /**
     * Test the fullRefund method with invalid data
     * @dataProvider fullRefundInvalidProvider
     * @return void
     * @throws PaymentServiceException|ParametersException
 */
    public function testInvalidFullRefund($data, $expectedException)
    {
        // PaymentService
        $paymentService = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        // Expectations
        $this->expectException($expectedException);

        // Call
        $this->callFullRefund($paymentService, $data);
    }

    /**
     * Ensure we can catch API error
     * @throws PaymentServiceException|ParametersException
     */
    public function testFullRefundRequestError()
    {
        // Params
        $id = "some_id";

        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->badPaymentResponseMock);

        // PaymentService
        $paymentService = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        // Expectations
        $this->expectException(PaymentServiceException::class);

        // Call
        $paymentService->fullRefund($id);
    }

    /**
     * Ensure we can catch RequestException
     * @throws ParametersException
     */
    public function testFullRefundRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Expectations
        $this->expectException(PaymentServiceException::class);

        // Call
        $paymentServiceMock->fullRefund('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @throws ParametersException
     */
    public function testFullRefundClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Expectations
        $this->expectException(PaymentServiceException::class);

        // Call
        $this->paymentService->fullRefund('id_1234');
    }

    /**
     * Ensure payment trigger is ok
     * @return void
     * @throws PaymentServiceException
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
     * @throws PaymentServiceException
     */
    public function testTriggerPaymentRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentServiceException::class);
        $paymentServiceMock->trigger('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentServiceException
     */
    public function testTriggerPaymentClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->trigger('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentServiceException
     */
    public function testTriggerPaymentPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->trigger('id_1234');
    }

    /**
     * Ensure payment edition is ok
     * @return void
     * @throws PaymentServiceException
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
     * @throws PaymentServiceException
     */
    public function testAddOrderRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPutRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentServiceException::class);
        $paymentServiceMock->addOrder('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentServiceException
     */
    public function testAddOrderClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->addOrder('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentServiceException
     */
    public function testAddOrderPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badOrderResponseMock);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->addOrder('id_1234');
    }

    /**
     * Ensure order overwrite is ok
     * @return void
     * @throws PaymentServiceException
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
     * @throws PaymentServiceException
     */
    public function testOverwriteOrderRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentServiceException::class);
        $paymentServiceMock->overwriteOrder('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentServiceException
     */
    public function testOverwriteOrderClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->overwriteOrder('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentServiceException
     */
    public function testOverwriteOrderPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badOrderResponseMock);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->overwriteOrder('id_1234');
    }


    /**
     * Ensure payment edition is ok
     * @return void
     * @throws PaymentServiceException
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
     * @throws PaymentServiceException
     */
    public function testAddOrderStatusByMerchantOrderReferenceRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentServiceException::class);
        $paymentServiceMock->addOrderStatusByMerchantOrderReference(
            'payment_id',
            'merchant_order_reference',
            'status'
        );
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentServiceException
     */
    public function testAddOrderStatusByMerchantOrderReferenceClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->addOrderStatusByMerchantOrderReference(
            'payment_id',
            'merchant_order_reference',
            'status'
        );
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentServiceException
     */
    public function testAddOrderStatusByMerchantOrderReferencePaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->addOrderStatusByMerchantOrderReference(
            'payment_id',
            'merchant_order_reference',
            'status'
        );
    }

    /**
     * Ensure payment edition is ok
     * @return void
     * @throws PaymentServiceException
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
     * @throws PaymentServiceException
     */
    public function testSendSmsRequestException()
    {
        // Mocks
        $paymentServiceMock = Mockery::mock(PaymentEndpoint::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $paymentServiceMock->shouldReceive('createPostRequest')->andThrow(new RequestException("request error"));

        // Call
        $this->expectException(PaymentServiceException::class);
        $paymentServiceMock->sendSms('id_1234');
    }

    /**
     * Ensure we can catch ClientException
     * @return void
     * @throws PaymentServiceException
     */
    public function testSendSmsClientException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andThrow(ClientException::class);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->sendSms('id_1234');
    }

    /**
     * Ensure we can catch API error
     * @return void
     * @throws PaymentServiceException
     */
    public function testSensSmsPaymentServiceException()
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->andReturn($this->badPaymentResponseMock);

        // Call
        $this->expectException(PaymentServiceException::class);
        $this->paymentService->sendSms('id_1234');
    }
}
