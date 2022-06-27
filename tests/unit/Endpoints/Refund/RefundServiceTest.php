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
use Alma\API\Endpoints\Payments\Refund\RefundService;

/**
 * Class RefundServiceTest
 */
class RefundServiceTest extends TestCase
{
    /**
     * MOCKS
     */

    private function mockResponse() {
        $json = '{"payment_plan":[{"customer_can_postpone":false,"customer_fee":100,"customer_interest":0,"date_paid":1649672472,"due_date":1649672451,"id":"installment_11uPRjPgPJ5jxmEEXxIPPOvVA3Fh7cg13m","original_purchase_amount":11733,"purchase_amount":11733,"state":"paid"},{"customer_can_postpone":false,"customer_fee":0,"customer_interest":0,"date_paid":null,"due_date":1652264451,"id":"installment_11uPRjP4a3cFQvoM1lehDnpxa2tYm2Hy0I","original_purchase_amount":11733,"purchase_amount":0,"state":"paid"},{"customer_can_postpone":false,"customer_fee":0,"customer_interest":0,"date_paid":null,"due_date":1654942851,"id":"installment_11uPRjP79fG5QtHO54lSYsnDvyHb56oeCn","original_purchase_amount":11733,"purchase_amount":0,"state":"paid"}],"orders":[{"comment":null,"created":1649672451,"customer_url":null,"data":{},"id":"order_11uPRjP4L9Dgbttx3cFUKGFPppdZIlrR2V","merchant_reference":"00000206","merchant_url":null,"payment":"payment_11uPRjP5FaIYygQao8WdMQFnKRMOV14frx"}],"refunds":[{"amount":35299,"created":1649770695,"from_payment_cancellation":false,"id":"refund_11uPrHz4TfHmQrD1OkYyWb4hPuq3673Vqm","merchant_reference":null,"payment":"payment_11uPRjP5FaIYygQao8WdMQFnKRMOV14frx","payment_refund_amount":11833,"rebate_amount":23466}]}';
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(false);
        $responseMock->json = json_decode($json, true);

        return $responseMock;
    }

    private function mockErrorResponse() {
        $json = '{"payment_plan":[{"customer_can_postpone":false,"customer_fee":100,"customer_interest":0,"date_paid":1649672472,"due_date":1649672451,"id":"installment_11uPRjPgPJ5jxmEEXxIPPOvVA3Fh7cg13m","original_purchase_amount":11733,"purchase_amount":11733,"state":"paid"},{"customer_can_postpone":false,"customer_fee":0,"customer_interest":0,"date_paid":null,"due_date":1652264451,"id":"installment_11uPRjP4a3cFQvoM1lehDnpxa2tYm2Hy0I","original_purchase_amount":11733,"purchase_amount":0,"state":"paid"},{"customer_can_postpone":false,"customer_fee":0,"customer_interest":0,"date_paid":null,"due_date":1654942851,"id":"installment_11uPRjP79fG5QtHO54lSYsnDvyHb56oeCn","original_purchase_amount":11733,"purchase_amount":0,"state":"paid"}],"orders":[{"comment":null,"created":1649672451,"customer_url":null,"data":{},"id":"order_11uPRjP4L9Dgbttx3cFUKGFPppdZIlrR2V","merchant_reference":"00000206","merchant_url":null,"payment":"payment_11uPRjP5FaIYygQao8WdMQFnKRMOV14frx"}],"refunds":[{"amount":35299,"created":1649770695,"from_payment_cancellation":false,"id":"refund_11uPrHz4TfHmQrD1OkYyWb4hPuq3673Vqm","merchant_reference":null,"payment":"payment_11uPRjP5FaIYygQao8WdMQFnKRMOV14frx","payment_refund_amount":11833,"rebate_amount":23466}]}';
        $responseMock = Mockery::mock(Response::class);
        $responseMock->shouldReceive('isError')->andReturn(true);
        $responseMock->json = json_decode($json, true);
        $responseMock->errorMessage = "a very important error message";

        return $responseMock;
    }

    private function mockRequest($response = null) {
        if ($response === null) {
            $response = $this->mockResponse();
        }
        $requestMock = Mockery::mock(Request::class);
        $requestMock->shouldReceive('setRequestBody');
        $requestMock->shouldReceive('post')->andReturn($response);
        return $requestMock;
    }

    /**
     * DATA PROVIDERS
     */


    /**
     * Return input to test testPartialRefund
     * @return array[]
     */
    public function getPartialRefundData() {
        return [
            [[ 'some id', 20000 ]],
            [[ 'some id', 50000 ]],
            [[ 'some id', 50000, 'merchant ref' ]],
            [[ 'some id', 50000, 'merchant ref', 'some comment' ]],
            [[ 'some id', 50000, null, 'some comment without ref' ]],
        ];
    }

    /**
     * Return input to test testFullRefund
     * @return array[]
     */
    public function getFullRefundData() {
        return [
            [[ 'some id', ]],
            [[ 'some id', ]],
            [[ 'some id', 'merchant ref' ]],
            [[ 'some id', 'merchant ref', 'some comment' ]],
            [[ 'some id', null, 'some comment without ref' ]],
        ];
    }

    /**
     * Return input to test testFullRefund
     * @return array[]
     */
    public function getPartialRefundErrorData() {
        return [
            [[ 'negative_amount', -1], ParamsError::class],
            [[ '', 15000, 'no id'], ParamsError::class],
        ];
    }

    /**
     * TESTS
     */

    /**
     * Test the partialRefund method with valid datas
     * @dataProvider getPartialRefundData
     * @return void
     */
    public function testPartialRefundExist($data)
    {
        $refundServiceMock = Mockery::mock(RefundService::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $id = $data[0];

        $refundServiceMock->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->andReturn($this->mockRequest());

        $refundServiceMock->partialRefund(...$data);
    }


    /**
     * Test the fullRefund method with valid datas
     * @dataProvider getFullRefundData
     * @return void
     */
    public function testFullRefundExist($data)
    {
        $refundServiceMock = Mockery::mock(RefundService::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $id = $data[0];

        $refundServiceMock->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->andReturn($this->mockRequest());

        $refundServiceMock->fullRefund(...$data);
    }


    /**
     * Test the partialRefund method with valid datas
     * @dataProvider getPartialRefundErrorData
     * @return void
     */
    public function testRefundInputError($data, $expectedException)
    {
        $refundServiceMock = Mockery::mock(RefundService::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();
        $id = $data[0];

        $refundServiceMock->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->andReturn($this->mockRequest());

        $this->expectException($expectedException);

        $refundServiceMock->partialRefund(...$data);
    }

    public function testFullRefundRequestError()
    {
        $id = "some_id";

        $refundServiceMock = Mockery::mock(RefundService::class)
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $refundServiceMock
            ->shouldAllowMockingProtectedMethods()
            ->shouldReceive('request')
            ->with("/v1/payments/$id/refund")
            ->andReturn($this->mockRequest($this->mockErrorResponse()));

        $this->expectException(RequestError::class);

        $refundServiceMock->fullRefund($id);
    }



    public function tearDown()
    {
        RefundService::setInstance(null);
        Mockery::close();
    }
}
