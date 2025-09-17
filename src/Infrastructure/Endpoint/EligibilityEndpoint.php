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

namespace Alma\API\Infrastructure\Endpoint;

use Alma\API\Domain\Entity\EligibilityList;
use Alma\API\Domain\Entity\Eligibility;
use Alma\API\Infrastructure\Exception\Endpoint\EligibilityEndpointException;
use Alma\API\Infrastructure\Exception\ParametersException;
use Alma\API\Infrastructure\Exception\RequestException;
use Psr\Http\Client\ClientExceptionInterface;


class EligibilityEndpoint extends AbstractEndpoint
{
    const ELIGIBILITY_ENDPOINT = '/v2/payments/eligibility';

    /**
     * Ask for Eligibility of a payment plan.
     * @param array $data Payment data to check the eligibility for – same data format as payment creation,
     *                              except that only payment.purchase_amount is mandatory and payment.installments_count
     *                              can be an array of integers, to test for multiple eligible plans at once.
     * @return EligibilityList A list of Eligibility objects, one for each payment plan.
     * @throws EligibilityEndpointException
     */
    public function getEligibilityList(array $data = []): EligibilityList
    {
        try {
            $request = null;
            $request = $this->createPostRequest(self::ELIGIBILITY_ENDPOINT, $data);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new EligibilityEndpointException($e->getMessage(), $request);
        } catch (RequestException $e) {
            throw new EligibilityEndpointException($e->getMessage());
        }

        if ($response->isError()) {
            throw new EligibilityEndpointException($response->getReasonPhrase(), $request, $response);
        }

        $eligibilityList = new EligibilityList();
		foreach ($response->getJson() as $jsonEligibility) {
            try {
                $eligibility = new Eligibility($jsonEligibility);
            } catch (ParametersException $e) {
                throw new EligibilityEndpointException($e->getMessage(), $request, $response);
            }

            $eligibilityList->Add($eligibility);

			if (!$eligibility->isEligible()) {
				$this->logger->info(
					"Eligibility check failed for following reasons: " .
					var_export($eligibility->getReasons(), true)
				);
			}
		}

        return $eligibilityList;
    }
}
