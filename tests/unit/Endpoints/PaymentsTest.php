<?php

namespace Alma\API\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;

use Alma\API\Endpoints\Payments;
use Alma\API\Lib\ClientOptionsValidator;
use Alma\API\ClientContext;
use Alma\API\Request;
use Alma\API\ParamsError;
use Alma\API\RequestError;

/**
 * Class Payments
 */
class PaymentsTest extends TestCase
{
    /**
     * Ensure that the methods exists
     */
    public function testRefundMethodExist()
    {
        $clientContext = Mockery::mock(ClientContext::class);

        $payments = new Payments($clientContext);

        $this->assertEquals(true, method_exists($payments, 'partialRefund'));
        $this->assertEquals(true, method_exists($payments, 'fullRefund'));
        # ensure backward compatibility
        $this->assertEquals(true, method_exists($payments, 'refund'));
    }

    /**
     * Mock ClientContext, Response and Request to allow us to test
     * Payment without sending any requests
     */
    private function mockServerRequest() {
        // ClientContext
        $clientContext = Mockery::mock(ClientContext::class);
        $clientContext->shouldReceive('urlFor');
        $clientContext->shouldReceive('getUserAgentString');
        $clientContext->shouldReceive('forcedTLSVersion');

        // Response
        $json = '{"payment_plan":[{"customer_can_postpone":false,"customer_fee":100,"customer_interest":0,"date_paid":1649672472,"due_date":1649672451,"id":"installment_11uPRjPgPJ5jxmEEXxIPPOvVA3Fh7cg13m","original_purchase_amount":11733,"purchase_amount":11733,"state":"paid"},{"customer_can_postpone":false,"customer_fee":0,"customer_interest":0,"date_paid":null,"due_date":1652264451,"id":"installment_11uPRjP4a3cFQvoM1lehDnpxa2tYm2Hy0I","original_purchase_amount":11733,"purchase_amount":0,"state":"paid"},{"customer_can_postpone":false,"customer_fee":0,"customer_interest":0,"date_paid":null,"due_date":1654942851,"id":"installment_11uPRjP79fG5QtHO54lSYsnDvyHb56oeCn","original_purchase_amount":11733,"purchase_amount":0,"state":"paid"}],"orders":[{"comment":null,"created":1649672451,"customer_url":null,"data":{},"id":"order_11uPRjP4L9Dgbttx3cFUKGFPppdZIlrR2V","merchant_reference":"00000206","merchant_url":null,"payment":"payment_11uPRjP5FaIYygQao8WdMQFnKRMOV14frx"}],"refunds":[{"amount":35299,"created":1649770695,"from_payment_cancellation":false,"id":"refund_11uPrHz4TfHmQrD1OkYyWb4hPuq3673Vqm","merchant_reference":null,"payment":"payment_11uPRjP5FaIYygQao8WdMQFnKRMOV14frx","payment_refund_amount":11833,"rebate_amount":23466}]}';
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
     * Return input to test testPartialRefund
     * @return array[]
     */
    public function getPartialRefundData() {
        return [
            [[
                'id' => "some_id",
                'amount' => 15000,
                'merchant_ref' => "merchant ref",
                'comment' => "some comment"
            ]],
            [[
                'id' => "some_id",
                'amount' => 15000,
                'merchant_ref' => "merchant ref"
            ]],
            [[
                'id' => "some_id",
                'amount' => 15000
            ]]
        ];
    }

    private function callPartialRefund($payments, $data) {
        if ( isset($data['merchant_ref']) && isset($data['comment']) ) {
            $payments->partialRefund($data['id'], $data['amount'], $data['merchant_ref'], $data['comment']);
        } else if (isset($data['merchant_ref'])) {
            $payments->partialRefund($data['id'], $data['amount'], $data['merchant_ref']);
        } else if (isset($data['comment'])) {
            $payments->partialRefund($data['id'], $data['amount'], '', $data['comment']);
        } else {
            $payments->partialRefund($data['id'], $data['amount']);
        }
    }

    /**
     * Test the partialRefund method with valid datas
     * @dataProvider getPartialRefundData
     * @return void
     */
    public function testPartialRefund($data)
    {
        $clientContext = Mockery::mock(ClientContext::class);

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial()
        ;
        $id = $data['id'];
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->once()
            ->andReturn($this->mockServerRequest())
        ;
        $payments->setClientContext($clientContext);

        /* Test */
        $this->callPartialRefund($payments, $data);
    }

    /**
     * Return invalid input to test testPartialRefund
     * @return array[]
     */
    public function getPartialRefundInvalidData() {
        return [
            [[
                'id' => "negative_amount",
                'amount' => -1
            ], ParamsError::class],
            [[
                'id' => "",
                'amount' => 1500,
                'merchant_ref' => "no id",
            ], ParamsError::class],
        ];
    }

    /**
     * Test the partialRefund method with valid datas
     * @dataProvider getPartialRefundInvalidData
     * @return void
     */
    public function testInvalidPartialRefund($data, $expectedException)
    {
        $clientContext = Mockery::mock(ClientContext::class);

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial()
        ;
        $id = $data['id'];
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->andReturn($this->mockServerRequest())
        ;
        $payments->setClientContext($clientContext);

        $this->expectException($expectedException);

        /* Test */
        $this->callPartialRefund($payments, $data);
    }


    private function callFullRefund($payments, $data) {
        if ( isset($data['merchant_ref']) && isset($data['comment']) ) {
            $payments->fullRefund($data['id'], $data['merchant_ref'], $data['comment']);
        } else if (isset($data['merchant_ref'])) {
            $payments->fullRefund($data['id'], $data['merchant_ref']);
        } else if (isset($data['comment'])) {
            $payments->fullRefund($data['id'], '', $data['comment']);
        } else {
            $payments->fullRefund($data['id']);
        }
    }

    /**
     * Return input to test testFullRefund
     * @return array[]
     */
    public function getFullRefundData() {
        return [
            [[
                'id' => "some_id",
                'merchant_ref' => "merchant ref",
                'comment' => "some comment"
            ]],
            [[
                'id' => "some_id",
                'merchant_ref' => "merchant ref"
            ]],
            [[
                'id' => "some_id",
            ]]
        ];
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
            ->makePartial()
        ;
        $id = $data['id'];
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->once()
            ->andReturn($this->mockServerRequest())
        ;
        $payments->setClientContext($clientContext);

        /* Test */
        $this->callFullRefund($payments, $data);
    }

    /**
     * Return input to test testRefund
     * @return array[]
     */
    public function getRefundData() {
        return [
            [[
                'id' => "some_id",
                'amount' => 15000,
                'merchant_ref' => "merchant ref",
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

    private function callRefund($payments, $data) {
        if (isset($data['merchant_ref']) && isset($data['amount'])) {
            $payments->refund($data['id'], $data['amount'], $data['merchant_ref']);
        } else if (isset($data['amount'])){
            $payments->refund($data['id'], $data['amount']);
        } else {
            $payments->refund($data['id']);
        }
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
            ->makePartial()
        ;
        $id = $data['id'];
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->once()
            ->andReturn($this->mockServerRequest())
        ;
        $payments->setClientContext($clientContext);

        /* Test */
        $this->callRefund($payments, $data);
    }

    /**
     * Return invalid input to test testFullRefund
     * @return array[]
     */
    public function getFullRefundInvalidData() {
        return [
            [[
                'id' => "",
                'merchant_ref' => "no id",
            ], ParamsError::class],
        ];
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
            ->makePartial()
        ;
        $id = $data['id'];
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->andReturn($this->mockServerRequest())
        ;
        $payments->setClientContext($clientContext);

        $this->expectException($expectedException);

        /* Test */
        $this->callFullRefund($payments, $data);
    }

    /**
     * Mock ClientContext, Response and Request to allow us to test
     * Payment without sending any requests but returns an error
     */
    private function mockServerRequestError() {
        // ClientContext
        $clientContext = Mockery::mock(ClientContext::class);
        $clientContext->shouldReceive('urlFor');
        $clientContext->shouldReceive('getUserAgentString');
        $clientContext->shouldReceive('forcedTLSVersion');

        // Response
        $json = '{"payment_plan":[{"customer_can_postpone":false,"customer_fee":100,"customer_interest":0,"date_paid":1649672472,"due_date":1649672451,"id":"installment_11uPRjPgPJ5jxmEEXxIPPOvVA3Fh7cg13m","original_purchase_amount":11733,"purchase_amount":11733,"state":"paid"},{"customer_can_postpone":false,"customer_fee":0,"customer_interest":0,"date_paid":null,"due_date":1652264451,"id":"installment_11uPRjP4a3cFQvoM1lehDnpxa2tYm2Hy0I","original_purchase_amount":11733,"purchase_amount":0,"state":"paid"},{"customer_can_postpone":false,"customer_fee":0,"customer_interest":0,"date_paid":null,"due_date":1654942851,"id":"installment_11uPRjP79fG5QtHO54lSYsnDvyHb56oeCn","original_purchase_amount":11733,"purchase_amount":0,"state":"paid"}],"orders":[{"comment":null,"created":1649672451,"customer_url":null,"data":{},"id":"order_11uPRjP4L9Dgbttx3cFUKGFPppdZIlrR2V","merchant_reference":"00000206","merchant_url":null,"payment":"payment_11uPRjP5FaIYygQao8WdMQFnKRMOV14frx"}],"refunds":[{"amount":35299,"created":1649770695,"from_payment_cancellation":false,"id":"refund_11uPrHz4TfHmQrD1OkYyWb4hPuq3673Vqm","merchant_reference":null,"payment":"payment_11uPRjP5FaIYygQao8WdMQFnKRMOV14frx","payment_refund_amount":11833,"rebate_amount":23466}]}';
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

    public function testFullRefundRequestError()
    {
        // Input
        $clientContext = Mockery::mock(ClientContext::class);
        $id = "some_id";

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial()
        ;
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->once()
            ->andReturn($this->mockServerRequestError())
        ;
        $payments->setClientContext($clientContext);

        $this->expectException(RequestError::class);

        /* Test */
        $payments->fullRefund($id);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
