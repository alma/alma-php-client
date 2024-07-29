<?php

namespace Alma\API\Tests\Unit\Endpoints;

use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

use Alma\API\Endpoints\Payments;
use Alma\API\ClientContext;
use Alma\API\Request;

/**
 * Class Payments
 */
class PaymentsTest extends TestCase
{
    const MERCHANT_REF = "merchant_ref";
    const SERVER_REQUEST_ERROR_RESPONSE_JON = '{
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

    public function tearDown(): void
    {
        Mockery::close();
    }

    /**
     * Return input to test testPartialRefund
     * @return array[]
     */
    public static function getPartialRefundData()
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
     * Return invalid input to test testPartialRefund
     * @return array[]
     */
    public static function getPartialRefundInvalidData()
    {
        return [
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
     * Return input to test testFullRefund
     * @return array[]
     */
    public static function getFullRefundData()
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
     * Return input to test testRefund
     * @return array[]
     */
    public static function getRefundData()
    {
        return [
            [[
                'id' => "some_id",
                'amount' => 15000,
                'merchant_ref' => self::MERCHANT_REF,
            ]],
            [[
                'id' => "some_id",
            ]],
            [[
                'id' => "some_id",
                'amount' => 15000
            ]]
        ];
    }

    /**
     * Return invalid input to test testFullRefund
     * @return array[]
     */
    public static function getFullRefundInvalidData()
    {
        return [
            [[
                'id' => "",
                'merchant_ref' => "no id",
            ], ParametersException::class],
        ];
    }

    /**
     * Mock ClientContext, Response and Request to allow us to test
     * Payment without sending any requests
     */
    private function mockServerRequest()
    {
        // ClientContext
        $clientContext = Mockery::mock(ClientContext::class);
        $clientContext->shouldReceive('urlFor');
        $clientContext->shouldReceive('getUserAgentString');
        $clientContext->shouldReceive('forcedTLSVersion');

        // Response
        $json = self::SERVER_REQUEST_ERROR_RESPONSE_JON;

        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->json = json_decode($json, true);

        // Request
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('setRequestBody');
        $requestMock->shouldReceive('post')->andReturn($responseMock);
        return $requestMock;
    }

    /**
     * @param Payments $payments
     * @param $data
     * @return void
     * @throws RequestException
     */
    private function callPartialRefund($payments, $data)
    {
        if (isset($data['merchant_ref']) && isset($data['comment'])) {
            $payments->partialRefund($data['id'], $data['amount'], $data['merchant_ref'], $data['comment']);
        } elseif (isset($data['merchant_ref'])) {
            $payments->partialRefund($data['id'], $data['amount'], $data['merchant_ref']);
        } elseif (isset($data['comment'])) {
            $payments->partialRefund($data['id'], $data['amount'], '', $data['comment']);
        } else {
            $payments->partialRefund($data['id'], $data['amount']);
        }
    }

    private function callFullRefund($payments, $data)
    {
        if (isset($data['merchant_ref']) && isset($data['comment'])) {
            $payments->fullRefund($data['id'], $data['merchant_ref'], $data['comment']);
        } elseif (isset($data['merchant_ref'])) {
            $payments->fullRefund($data['id'], $data['merchant_ref']);
        } elseif (isset($data['comment'])) {
            $payments->fullRefund($data['id'], '', $data['comment']);
        } else {
            $payments->fullRefund($data['id']);
        }
    }

    private function callRefund($payments, $data)
    {
        if (isset($data['merchant_ref']) && isset($data['amount'])) {
            $payments->refund($data['id'], $data['amount'], $data['merchant_ref']);
        } elseif (isset($data['amount'])) {
            $payments->refund($data['id'], $data['amount']);
        } else {
            $payments->refund($data['id']);
        }
    }

    /**
     * Mock ClientContext, Response and Request to allow us to test
     * Payment without sending any requests but returns an error
     */
    private function mockServerRequestError()
    {
        // ClientContext
        $clientContext = Mockery::mock(ClientContext::class);
        $clientContext->shouldReceive('urlFor');
        $clientContext->shouldReceive('getUserAgentString');
        $clientContext->shouldReceive('forcedTLSVersion');

        // Response
        $json = self::SERVER_REQUEST_ERROR_RESPONSE_JON;

        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(true);
        $responseMock->json = json_decode($json, true);
        $responseMock->errorMessage = "a very important error message";

        // Request
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('setRequestBody');
        $requestMock->shouldReceive('post')->andReturn($responseMock);
        return $requestMock;
    }

    /**
     * Ensure that the methods exists
     */
    public function testRefundMethodExist()
    {
        $clientContext = Mockery::mock(ClientContext::class);

        $payments = new Payments($clientContext);

        $this->assertTrue(method_exists($payments, 'partialRefund'));
        $this->assertTrue(method_exists($payments, 'fullRefund'));
        # ensure backward compatibility
        $this->assertTrue(method_exists($payments, 'refund'));
    }

    /**
     * Test the partialRefund method with valid datas
     * @dataProvider getPartialRefundData
     * @param $data
     * @return void
     * @throws RequestException
     */
    public function testPartialRefund($data)
    {
        $clientContext = Mockery::mock(ClientContext::class);

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $id = $data['id'];
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->once()
            ->andReturn($this->mockServerRequest());
        $payments->setClientContext($clientContext);

        /* Test */
        $this->callPartialRefund($payments, $data);
    }

    /**
     * Test the partialRefund method with valid datas
     * @dataProvider getPartialRefundInvalidData
     * @param $data
     * @param $expectedException
     * @return void
     * @throws RequestException
     */
    public function testInvalidPartialRefund($data, $expectedException)
    {
        $clientContext = Mockery::mock(ClientContext::class);

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $id = $data['id'];
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->andReturn($this->mockServerRequest());
        $payments->setClientContext($clientContext);

        $this->expectException($expectedException);

        /* Test */
        $this->callPartialRefund($payments, $data);
    }

    /**
     * Test the fullRefund method with valid datas
     * @dataProvider getFullRefundData
     * @return void
     */
    public function testFullRefund($data)
    {
        $clientContext = Mockery::mock(ClientContext::class);

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $id = $data['id'];
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->once()
            ->andReturn($this->mockServerRequest());
        $payments->setClientContext($clientContext);

        /* Test */
        $this->callFullRefund($payments, $data);
    }

    /**
     * Test the fullRefund method with valid datas
     * Important to ensure we didn't break compatibility with 1.x.x versions
     * @dataProvider getRefundData
     * @return void
     */
    public function testRefund($data)
    {
        $clientContext = Mockery::mock(ClientContext::class);

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $id = $data['id'];
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->once()
            ->andReturn($this->mockServerRequest());
        $payments->setClientContext($clientContext);

        /* Test */
        $this->callRefund($payments, $data);
    }

    /**
     * Test the fullRefund method with valid datas
     * @dataProvider getFullRefundInvalidData
     * @return void
     */
    public function testInvalidFullRefund($data, $expectedException)
    {
        $clientContext = Mockery::mock(ClientContext::class);

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $id = $data['id'];
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->andReturn($this->mockServerRequest());
        $payments->setClientContext($clientContext);

        $this->expectException($expectedException);

        /* Test */
        $this->callFullRefund($payments, $data);
    }

    public function testFullRefundRequestError()
    {
        // Input
        $clientContext = Mockery::mock(ClientContext::class);
        $id = "some_id";

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->once()
            ->andReturn($this->mockServerRequestError());
        $payments->setClientContext($clientContext);

        $this->expectException(RequestException::class);

        /* Test */
        $payments->fullRefund($id);
    }

}
