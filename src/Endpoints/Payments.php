<?php
/**
 * Copyright (c) 2018 Alma / Nabla SAS
 *
 * THE MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and
 * to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @author    Alma / Nabla SAS <contact@getalma.eu>
 * @copyright Copyright (c) 2018 Alma / Nabla SAS
 * @license   https://opensource.org/licenses/MIT The MIT License
 */

namespace Alma\API\Endpoints;

use Alma\API\Endpoints\Results\Eligibility;
use Alma\API\Entities\Order;
use Alma\API\Entities\Payment;
use Alma\API\Lib\ArrayUtils;
use Alma\API\RequestError;
use Alma\API\Response;
use Alma\API\Endpoints\Payments\Refund\RefundService;
use Alma\API\Endpoints\Payments\Eligibility\EligibilityService;

class Payments extends Base
{
    const PAYMENTS_PATH       = '/v1/payments';

    /**
     * @param array $data         Payment data to check the eligibility for – same data format as payment
     *                            creation, except that only payment.purchase_amount is mandatory and
     *                            payment.installments_count can be an array of integers, to test for multiple
     *                            eligible plans at once.
     * @param bool  $raiseOnError Whether to raise a RequestError on 4xx and 5xx errors, as it should.
     *                            Defaults false to preserve original behaviour. Will default to true
     *                            in future versions (next major update).
     *
     * @return Eligibility[]
     * @throws RequestError
     */
    public function eligibility(array $data, $raiseOnError = false)
    {
        $eligibilityService = EligibilityService::getInstance($this->clientContext);

        if ($eligibilityService->isV1Payload($data)) {
            return $eligibilityService->eligibilityV1($data, $raiseOnError);
        }
        return $eligibilityService->eligibility($data, $raiseOnError);
    }

    /**
     * @param array $data
     *
     * @return Payment
     * @throws RequestError
     */
    public function create($data)
    {
        $res = $this->request(self::PAYMENTS_PATH)->setRequestBody($data)->post();

        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }

        return new Payment($res->json);
    }

    /**
     * @param array $data
     *
     * @return Payment
     * @throws RequestError
     *
     * @deprecated Use Payments::create() instead
     */
    public function createPayment($data)
    {
        return $this->create($data);
    }


    /**
     * @param string $id The external ID for the payment to fetch
     *
     * @return Payment
     * @throws RequestError
     */
    public function fetch($id)
    {
        $res = $this->request(self::PAYMENTS_PATH . "/$id")->get();

        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }

        return new Payment($res->json);
    }

    /**
     * @param string $id
     * @param array  $data
     *
     * @return Payment
     * @throws RequestError
     */
    public function edit($id, $data)
    {
        $res = $this->request(self::PAYMENTS_PATH . "/$id")->setRequestBody($data)->post();

        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }

        return new Payment($res->json);
    }

    /**
     * @param string $id     The ID of the payment to flag as potential fraud
     * @param string $reason An optional message indicating why this payment is being flagged
     *
     * @return bool
     * @throws RequestError
     */
    public function flagAsPotentialFraud($id, $reason=null)
    {
        $req = $this->request(self::PAYMENTS_PATH . "/$id/potential-fraud");

        if (!empty($reason)) {
            $req->setRequestBody(array("reason" => $reason));
        }

        $res = $req->post();
        if ($res->isError()) {
            throw new RequestError($res->errorMessage, $req, $res);
        }

        return true;
    }

    /**
     * Refund a payment partially
     *
     * @param string $id                ID of the payment to be refunded
     * @param int    $amount            Amount that should be refunded. Must be expressed as a cents
     *                                  integer
     * @param string $merchantReference Merchant reference for the refund to be executed
     * @param string $comment
     *
     * @return Payment
     * @throws RequestError
     */
    public function partialRefund($id, $amount, $merchantReference = "", $comment = "")
    {
        $refundService = RefundService::getInstance($this->clientContext);

        return $refundService->partialRefund($id, $amount, $merchantReference, $comment);
    }

    /**
     * Totally refund a payment
     *
     * @param string $id                ID of the payment to be refunded
     * @param string $merchantReference Merchant reference for the refund to be executed
     * @param string $comment
     *
     * @return Payment
     * @throws RequestError
     */
    public function fullRefund($id, $merchantReference = "", $comment = "")
    {
        $refundService = RefundService::getInstance($this->clientContext);

        return $refundService->fullRefund($id, $merchantReference, $comment);
    }

    /**
     * @param string $id                ID of the payment to be refunded
     * @param bool   $totalRefund       Should the payment be completely refunded? In this case, $amount is not required as the
     *                                  API will automatically compute the amount to refund, including possible customer fees
     * @param int    $amount            Amount that should be refunded, for a partial refund. Must be expressed as a cents
     *                                  integer
     * @param string $merchantReference Merchant reference for the refund to be executed
     *
     * @return Payment
     * @throws RequestError
     *
     * @deprecated please use `partialRefund` or `fullRefund`
     */
    public function refund($id, $totalRefund = true, $amount = null, $merchantReference = "")
    {
        if ($totalRefund !== true) {
            return $this->partialRefund($id, $amount, $merchantReference);
        }
        return $this->fullRefund($id, $merchantReference);
    }

    /**
     * @param string $id ID of the payment to be triggered
     *
     * @return Payment
     * @throws RequestError
     */
    public function trigger($id)
    {
        $req = $this->request(self::PAYMENTS_PATH . "/$id/trigger");

        $res = $req->post();
        if ($res->isError()) {
            throw new RequestError($res->errorMessage, $req, $res);
        }

        return new Payment($res->json);
    }

    /**
     * Adds an Order to the given Payment, possibly overwriting existing orders
     *
     * @param string $id        ID of the payment to which the order must be added
     * @param array  $orderData Data of the Order
     * @param bool   $overwrite Should the order replace any other order set on the payment, or be appended to the payment's orders (default: false)
     *
     * @return Order
     *
     * @throws RequestError
     */
    public function addOrder($id, $orderData, $overwrite = false)
    {
        $req = $this->request(self::PAYMENTS_PATH . "/$id/orders")->setRequestBody(array("order" => $orderData));

        if ($overwrite) {
            $res = $req->post();
        } else {
            $res = $req->put();
        }

        return new Order(end($res->json));
    }

    /**
     * Sends a SMS to the customer, containing a link to the payment's page
     * /!\ Your account must be authorized by Alma to use that endpoint; it will otherwise fail with a 403 error
     *
     * @param string $id ID of the payment to send a SMS for
     *
     * @return bool
     *
     * @throws RequestError
     */
    public function sendSms($id)
    {
        $res = $this->request(self::PAYMENTS_PATH . "/$id/send-sms")->post();
        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }

        return true;
    }

}
