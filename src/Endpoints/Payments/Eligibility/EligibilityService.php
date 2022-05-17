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

namespace Alma\API\Endpoints\Payments\Eligibility;

use Alma\API\Lib\ServiceBase;
use Alma\API\Endpoints\Results\Eligibility;

class EligibilityService extends ServiceBase
{
    const ELIGIBILITY_PATH_V1 = '/v1/payments/eligibility';
    const ELIGIBILITY_PATH    = '/v2/payments/eligibility';

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
     *
     * @deprecated
     */
    public function eligibilityV1(array $data, $raiseOnError = false)
    {
        $res = $this->request(self::ELIGIBILITY_PATH_V1)->setRequestBody($data)->post();

        if ($res->isError()) {
            if ($raiseOnError) {
                throw new RequestError($res->errorMessage, null, $res);
            }

            return [new Eligibility($res->json, $res->responseCode)];
        }


        if (is_array($res->json)) {
            $result = [];
            foreach ($res->json as $eligibilityData) {
                $eligibility = $this->buildEligibilityObject($eligibilityData, $res->responseCode);
                $result[$eligibility->getInstallmentsCount()] = $eligibility;
            }
            return $result;
        }

        return [
            new Eligibility(
                [
                    "eligible" => false,
                    "reasons"  => ["Unexpected value from eligibility: " . var_export($res->json, true)],
                ], $res->responseCode
            )
        ];
    }

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
        $res = $this->request(self::ELIGIBILITY_PATH)->setRequestBody($data)->post();

        if ($res->isError()) {
            if ($raiseOnError) {
                throw new RequestError($res->errorMessage, null, $res);
            }

            return [
                new Eligibility(
                    [
                        "eligible" => false,
                        "reasons"  => ["Unexpected value from eligibility: " . var_export($res->json, true)],
                    ], $res->responseCode
                )
            ];
        }

        if (is_array($res->json)) {
            $result = [];
            foreach ($res->json as $eligibilityData) {
                $eligibility = $this->buildEligibilityObject($eligibilityData, $res->responseCode);
                $result[$eligibility->getPlanKey()] = $eligibility;
            }
            return $result;
        }

        return [
            new Eligibility(
                [
                "eligible" => false,
                "reasons"  => ["Unexpected value from eligibility: " . var_export($res->json, true)],
                ], $res->responseCode
            )
        ];
    }

    protected function buildEligibilityObject($eligibilityData, $responseCode)
    {
        $eligibility = new Eligibility($eligibilityData, $responseCode);

        if (!$eligibility->isEligible()) {
            $this->getLogger()->info(
                "Eligibility check failed for following reasons: " .
                var_export($eligibility->reasons, true)
            );
        }

        return $eligibility;
    }

    public function isV1Payload($data)
    {
        return array_key_exists('payment', $data);
    }

}