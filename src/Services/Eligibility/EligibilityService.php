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

namespace Alma\API\Services\Eligibility;

use Alma\API\Exceptions\RequestException;
use Alma\API\Services\ServiceBase;
use Alma\API\Services\PayloadInterface;
use Alma\API\Endpoints\Results\Eligibility;
use Exception;

class EligibilityService extends ServiceBase
{
    const ELIGIBILITY_PATH = '/v2/payments/eligibility';

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

    /**
     * @param PayloadInterface $payload
     *
     * @return Eligibility[]
     * @throws RequestException
     */
    public function getList(PayloadInterface $payload = null)
    {
        $payload->validate();
        $request  = $this->request(self::ELIGIBILITY_PATH);
        $response = $request
            ->setRequestBody($payload->toPayload())
            ->post();

        if ($response->isError()) {
            throw new RequestException($response->errorMessage, $request, $response);
        }

        if (is_array($response->json)) {
            $list = [];
            foreach ($response->json as $eligibilityData) {
                $eligibility = $this->buildEligibilityObject(
                    $eligibilityData,
                    $response->responseCode
                );

                $list[$eligibility->getPlanKey()] = $eligibility;
            }

            return $list;
        }

        throw new RequestException("Bad response format", $request, $response);
    }

}
