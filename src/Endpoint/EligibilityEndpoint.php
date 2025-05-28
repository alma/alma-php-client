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

namespace Alma\API\Endpoint;

use Alma\API\ClientConfiguration;
use Alma\API\Endpoint\Result\Eligibility;
use Alma\API\Exceptions\EligibilityServiceException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Lib\ArrayUtils;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class EligibilityEndpoint extends AbstractEndpoint
{
    const ELIGIBILITY_ENDPOINT = '/v2/payments/eligibility';

    /**
     * Ask for Eligibility of a payment plan.
     * @param array $data Payment data to check the eligibility for – same data format as payment creation,
     *                              except that only payment.purchase_amount is mandatory and payment.installments_count
     *                              can be an array of integers, to test for multiple eligible plans at once.
     * @return Eligibility[]
     * @throws EligibilityServiceException
     */
    public function eligibility(array $data): array
    {
        try {
            $request = null;
            $request = $this->createPostRequest(self::ELIGIBILITY_ENDPOINT, $data);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new EligibilityServiceException($e->getMessage(), $request);
        } catch (RequestException $e) {
            throw new EligibilityServiceException($e->getMessage());
        }

        if ($response->isError()) {
            throw new EligibilityServiceException($response->getReasonPhrase(), $request, $response);
        }

		$result = [];
		foreach ($response->getJson() as $jsonEligibility) {
			$eligibility = new Eligibility($jsonEligibility);
			$result[$eligibility->getPlanKey()] = $eligibility;

			if (!$eligibility->isEligible()) {
				$this->logger->info(
					"Eligibility check failed for following reasons: " .
					var_export($eligibility->reasons, true)
				);
			}
		}

        return $result;
    }
}
