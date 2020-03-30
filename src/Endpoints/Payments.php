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
 *
 */

namespace Alma\API\Endpoints;

use Alma\API\Endpoints\Results\Eligibility;
use Alma\API\Entities\Order;
use Alma\API\Entities\Payment;
use Alma\API\RequestError;

class Payments extends Base
{
    const PAYMENTS_PATH = '/v1/payments';

    /**
     * @param array $orderData
     *
     * @return Eligibility
     * @throws RequestError
     */
    public function eligibility($orderData)
    {
        $res = $this->request(self::PAYMENTS_PATH . '/eligibility')->setRequestBody($orderData)->post();

        $serverError = $res->responseCode >= 500;

        if (!$serverError && is_assoc_array($res->json)) {
            $result = new Eligibility($res->json, $res->responseCode);
            if (!$result->isEligible()) {
                $this->logger->info(
                    "Eligibility check failed for following reasons: " .
                    var_export($result->reasons, true)
                );
            }
        } elseif (!$serverError && is_array($res->json)) {
            $result = [];
            foreach ($res->json as $data) {
                $eligibility = new Eligibility($data, $res->responseCode);
                $result[$eligibility->getInstallmentsCount()] = $eligibility;
                if (!$eligibility->isEligible()) {
                    $this->logger->info(
                        "Eligibility check failed for following reasons: " .
                        var_export($eligibility->reasons, true)
                    );
                }
            }
        } else {
            $this->logger->info(
                "Unexpected value from eligibility: " . var_export($res->json, true)
            );

            $result = new Eligibility(array("eligible" => false), $res->responseCode);
        }

        return $result;
    }

    /**
     * @param $data
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
     * @param $data
     *
     * @return Payment
     * @throws RequestError
     * @deprecated Use Payments::create() instead
     */
    public function createPayment($data)
    {
        return $this->create($data);
    }


    /**
     * @param $id string The external ID for the payment to fetch
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
     * @param $id       string  The ID of the payment to flag as potential fraud
     * @param $reason   string  An optional message indicating why this payment is being flagged
     *
     * @return boolean
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
     * @param string $id ID of the payment to be refunded
     * @param bool $totalRefund Should the payment be completely refunded? In this case, $amount is not required as the
     *                          API will automatically compute the amount to refund, including possible customer fees
     * @param int $amount Amount that should be refunded, for a partial refund. Must be expressed as a cents
     *                          integer
     * @return Payment
     * @throws RequestError
     */
    public function refund($id, $totalRefund = true, $amount = null)
    {
        $req = $this->request(self::PAYMENTS_PATH . "/$id/refund");

        if (!$totalRefund) {
            $req->setRequestBody(array("amount" => $amount));
        }

        $res = $req->post();
        if ($res->isError()) {
            throw new RequestError($res->errorMessage, $req, $res);
        }

        return new Payment($res->json);
    }

    /**
     * Adds an Order to the given Payment, possibly overwriting existing orders
     *
     * @param string $id ID of the payment to which the order must be added
     * @param array $orderData Data of the Order
     * @param bool $overwrite Should the order replace any other order set on the payment, or be appended to the payment's orders (default: false)
     *
     * @return Order
     *
     * @throws RequestError
     */
    public function addOrder($id, $orderData, $overwrite = false)
    {
        $req = $this->request(self::PAYMENTS_PATH . "/$id/orders")->setRequestBody(array("order" => $orderData));

        $res = null;
        if ($overwrite) {
            $res = $req->post();
        } else {
            $res = $req->put();
        }

        return new Order(end($res->json));
    }

}
