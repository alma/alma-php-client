<?php

namespace Alma\API\Tests\Unit\Endpoint;

use Alma\API\Entities\Payment;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\PaymentServiceException;
use Alma\API\Response;
use Mockery;
use Alma\API\Endpoint\PaymentService;

/**
 * Class Payments
 */
class PaymentServiceTest extends AbstractEndpointService
{
    const MERCHANT_REF = "merchant_ref";

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
             "carrier":null,
             "tracking_number":null,
             "tracking_url":null,
             "comment":null,
             "created":1649672451,
             "customer_url":null,
             "data":{},
             "id":"order_11uPRjP4L9Dgbttx3cFUKGFPppdZIlrR2V",
             "merchant_reference":"00000206",
             "merchant_url":null,
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

    public function testCreate()
    {

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
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $responseMock->shouldReceive('getJson')
            ->andReturn(json_decode(self::SERVER_REQUEST_RESPONSE_JSON, true));
        $this->clientMock->shouldReceive('sendRequest')
            ->once()
            ->andReturn($responseMock);
        $paymentService = Mockery::mock(PaymentService::class, [$this->clientMock])
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
        $paymentService = Mockery::mock(PaymentService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        // Expectations
        $this->expectException($expectedException);

        // Call
        $this->callPartialRefund($paymentService, $data);
    }

    /**
     * @param PaymentService $paymentService
     * @param $data
     * @return void
     * @throws PaymentServiceException|ParametersException
     */
    private function callPartialRefund(PaymentService $paymentService, $data)
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
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $responseMock->shouldReceive('getJson')
            ->andReturn(json_decode(self::SERVER_REQUEST_RESPONSE_JSON, true));
        $this->clientMock->shouldReceive('sendRequest')
            ->once()
            ->andReturn($responseMock);

        // PaymentService
        $paymentService = Mockery::mock(PaymentService::class, [$this->clientMock])
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
    private function callFullRefund(PaymentService $paymentService, $data): Payment
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
        $paymentService = Mockery::mock(PaymentService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        // Expectations
        $this->expectException($expectedException);

        // Call
        $this->callFullRefund($paymentService, $data);
    }

    /**
     * @throws PaymentServiceException|ParametersException
     */
    public function testFullRefundRequestError()
    {
        // Params
        $id = "some_id";

        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(true);
        $responseMock->shouldReceive('getReasonPhrase')->andReturn('oops');
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // PaymentService
        $paymentService = Mockery::mock(PaymentService::class, [$this->clientMock])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        // Expectations
        $this->expectException(PaymentServiceException::class);

        // Call
        $paymentService->fullRefund($id);
    }

    public static function addIOrderStatusThrowRequestExceptionForNon200ReturnProvider(): array
    {
        return [
            'With is shipped false' => [
                'paymentId' => 'payment_42',
                'merchantOrderReference' => 'ref_3546',
                'status' => 'in progress',
                'isShipped' => false
            ],
        ];
    }

    /**
     * @dataProvider addIOrderStatusThrowRequestExceptionForNon200ReturnProvider
     * @param $paymentId
     * @param $merchantOrderReference
     * @param $status
     * @param null $isShipped
     * @return void
     * @throws PaymentServiceException
 */
    public function testAddIOrderStatusThrowRequestExceptionForNon200Return($paymentId, $merchantOrderReference, $status, $isShipped)
    {
        // Mocks
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(true);
        $responseMock->shouldReceive('getReasonPhrase')->andReturn('Error in request');
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($responseMock);

        // PaymentService
        $paymentService = Mockery::mock(PaymentService::class, [$this->clientMock])->makePartial();
        $paymentService->shouldReceive('createPostRequest')
            ->with(Mockery::on(function ($argument) use ($paymentId, $merchantOrderReference, $status, $isShipped) {
                $urlIsSet = isset($argument['url']) && $argument['url'] === PaymentService::PAYMENTS_ENDPOINT . "/$paymentId/orders/$merchantOrderReference/status";
                $bodyIsSet = isset($argument['body']) && $argument['body'] === json_encode([
                        'status' => $status,
                        'is_shipped' => $isShipped
                    ]);
                return $urlIsSet && $bodyIsSet;
            }));

        // Expectations
        $this->expectException(PaymentServiceException::class);

        // Call
        $paymentService->addOrderStatusByMerchantOrderReference($paymentId, $merchantOrderReference, $status, $isShipped);
    }

    public static function addOrderStatusReturnVoidFor204returnProvider(): array
    {
        return [
            'With is shipped null' => [
                'paymentId' => 'payment_1234',
                'merchantOrderReference' => 'merchant_order_123',
                'status' => 'status_shipped'
            ],
            'With is shipped bool' => [
                'paymentId' => 'payment_1234',
                'merchantOrderReference' => 'merchant_order_123',
                'status' => 'status_shipped',
                'isShipped' => true
            ]
        ];
    }

    /**
     * @dataProvider addOrderStatusReturnVoidFor204returnProvider
     * @param $paymentId
     * @param $merchantOrderReference
     * @param $status
     * @param null $isShipped
     * @return void
     * @throws PaymentServiceException
     */
    public function testAddOrderStatusReturnVoidFor204return(
        $paymentId,
        $merchantOrderReference,
        $status,
        $isShipped = null
    )
    {
        // Mocks
        $this->clientMock->shouldReceive('sendRequest')->once()->andReturn($this->responseMock);

        // PaymentService
        $paymentService = Mockery::mock(PaymentService::class, [$this->clientMock])->makePartial();
        $paymentService->shouldReceive('createPostRequest')
            ->with(Mockery::on(function ($argument) use ($paymentId, $merchantOrderReference, $status, $isShipped) {
                $urlIsSet = isset($argument['url']) && $argument['url'] === PaymentService::PAYMENTS_ENDPOINT . "/$paymentId/orders/$merchantOrderReference/status";
                $bodyIsSet = isset($argument['body']) && $argument['body'] === json_encode([
                    'status' => $status,
                    'is_shipped' => $isShipped
                ]);
                return $urlIsSet && $bodyIsSet;
            }));

        // Call
        $paymentService->addOrderStatusByMerchantOrderReference(
            $paymentId,
            $merchantOrderReference,
            $status,
            $isShipped
        );
    }
}
