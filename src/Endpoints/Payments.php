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

        if ($res->responseCode === 406) {
            $this->logger->info(
                "Eligibility check failed for following reasons: " .
                print_r($res->json["reasons"], true)
            );
        }

        return new Eligibility($res);
    }

    /**
     * @param $data
     *
     * @return Payment
     * @throws RequestError
     */
    public function createPayment($data)
    {
        $res = $this->request(self::PAYMENTS_PATH)->setRequestBody($data)->post();

        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }

        return new Payment($res->json);
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
}
