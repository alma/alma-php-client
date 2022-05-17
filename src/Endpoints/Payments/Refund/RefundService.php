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

namespace Alma\API\Endpoints\Payments\Refund;

use Alma\API\Entities\Payment;
use Alma\API\Lib\ServiceBase;
use Alma\API\Endpoints\Payments\Refund\RefundPayload as Refund;
use Alma\API\RequestError;

class RefundService extends ServiceBase
{
    const REFUND_PATH = '/v1/payments/%s/refund';

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
        return $this->_refund(
            Refund::create($id, $amount, $merchantReference, $comment)
        );
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
        return $this->_refund(
            Refund::create($id, 0, $merchantReference, $comment)
        );
    }

    /**
     * Call Alma Refund API
     *
     * @param Refund $refundPayload contains all the refund info
     *
     * @return Payment
     * @throws RequestError
     */
    private function _refund(Refund $refundPayload)
    {
        $req = $this->request(sprintf(self::REFUND_PATH, $refundPayload->getId()));

        $req->setRequestBody($refundPayload->getRequestBody());

        $res = $req->post();
        if ($res->isError()) {
            throw new RequestError($res->errorMessage, $req, $res);
        }

        return new Payment($res->json);
    }

}