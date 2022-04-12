<?php

namespace Alma\API\Tests\Unit;

use Mockery;
use PHPUnit\Framework\TestCase;

use Alma\API\Endpoints\Payments;
use Alma\API\Lib\ClientOptionsValidator;
use Alma\API\ClientContext;
use Alma\API\Request;

/**
 * Class Payments
 */
class PaymentsTest extends TestCase
{
    public function testRefundMethodExist()
    {
        $clientContext = [];
        $payments = new Payments($clientContext);

        $this->assertEquals(true, method_exists($payments, 'partialRefund'));
        $this->assertEquals(true, method_exists($payments, 'fullRefund'));
    }

    public function testPartialRefund()
    {
        /* Input */
        $id = 1;

        /* Mock */
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
        $requestMock->shouldReceive('setRequestBody')->once();
        $requestMock->shouldReceive('post')->andReturn($responseMock);

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial()
        ;
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->once()
            ->andReturn($requestMock)
        ;
        $payments->setClientContext($clientContext);

        /* Test */
        $payments->partialRefund($id, 15000, 'merchant ref', 'some comment');
    }

    public function testFullRefund()
    {
        /* Input */
        $id = 1;

        /* Mock */
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
        $requestMock->shouldReceive('setRequestBody')->once();
        $requestMock->shouldReceive('post')->andReturn($responseMock);

        // Payment
        $payments = Mockery::mock(Payments::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial()
        ;
        $payments->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->once()
            ->andReturn($requestMock)
        ;
        $payments->setClientContext($clientContext);

        /* Test */
        $payments->fullRefund($id, 'merchant ref', 'some comment');
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
