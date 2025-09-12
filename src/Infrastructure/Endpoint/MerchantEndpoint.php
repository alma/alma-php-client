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

use Alma\API\Application\DTO\MerchantBusinessEvent\CartInitiatedBusinessEventDto;
use Alma\API\Application\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEventDto;
use Alma\API\Domain\Entity\FeePlan;
use Alma\API\Domain\Entity\FeePlanList;
use Alma\API\Domain\Entity\Merchant;
use Alma\API\Infrastructure\Exception\Endpoint\MerchantEndpointException;
use Alma\API\Infrastructure\Exception\ParametersException;
use Alma\API\Infrastructure\Exception\RequestException;
use Psr\Http\Client\ClientExceptionInterface;

class MerchantEndpoint extends AbstractEndpoint
{
    const ME_ENDPOINT = '/v1/me';
    const BUSINESS_EVENTS_ENDPOINT = self::ME_ENDPOINT . '/business-events';
    const FEE_PLANS_ENDPOINT = self::ME_ENDPOINT . '/fee-plans';
    const EXTENDED_DATA_ENDPOINT = self::ME_ENDPOINT . '/extended-data';

    /**
     * @return Merchant
     * @throws MerchantEndpointException
     */
    public function me(): Merchant
    {
        try {
            $request = null;
            $request = $this->createGetRequest(self::EXTENDED_DATA_ENDPOINT);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new MerchantEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new MerchantEndpointException($response->getReasonPhrase(), $request, $response);
        }

        try {
            $merchant = new Merchant($response->getJson());
        } catch (ParametersException $e) {
            throw new MerchantEndpointException($e->getMessage(), $request, $response);
        }

        return $merchant;
    }

    /**
     * @param $kind string  Either FeePlan::KIND_GENERAL or FeePlan::KIND_POS. The former is applied to online payments,
     *                      while the latter will be used when creating a Payment with origin=pos_* for
     *                      retail/point-of-sale use cases. Defaults to FeePlan::KIND_GENERAL
     * @param string|int[] $installmentsCounts Only include fee plans that match the given installments counts, or use
     *                                         the string "all" (default) to get all available fee plans
     * @param bool $includeDeferred Include deferred fee plans (i.e. Pay Later plans) in the response
     * @return FeePlanList A list of available fee plans (some might be disabled, check FeePlan->allowed for each)
     *
     * @throws MerchantEndpointException
     */
    public function getFeePlanList(string $kind = FeePlan::KIND_GENERAL, $installmentsCounts = "all", bool $includeDeferred = false): FeePlanList
    {
        if (is_array($installmentsCounts)) {
            $only = implode(",", $installmentsCounts);
        } else {
            $only = $installmentsCounts;
        }

        $queryParams = array(
            "kind" => $kind,
            "only" => $only,
            "deferred" => $includeDeferred ? "true" : "false" // Avoid conversion to "0"/"1" our API doesn't recognize
        );
        try {
            $request = null;
            $request = $this->createGetRequest(self::FEE_PLANS_ENDPOINT, $queryParams);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new MerchantEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new MerchantEndpointException($response->getReasonPhrase(), $request, $response);
        }

        $feePlanList = new FeePlanList();
        foreach ($response->getJson() as $jsonFeePlan) {
            try {
                $feePlan = new FeePlan($jsonFeePlan);
            } catch (ParametersException $e) {
                throw new MerchantEndpointException($e->getMessage(), $request, $response);
            }
            $feePlanList->Add($feePlan);
        }

        return $feePlanList;
    }

    /**
     * Prepare and send a business event for a cart initiated
     *
     * @param CartInitiatedBusinessEventDto $cartEventData
     * @return void
     * @throws MerchantEndpointException
     */
    public function sendCartInitiatedBusinessEvent(CartInitiatedBusinessEventDto $cartEventData)
    {
        $this->sendBusinessEvent($cartEventData->toArray());
    }

    /**
     * Prepare and send a business event for Order confirmed
     *
     * @param OrderConfirmedBusinessEventDto $orderConfirmedBusinessEvent
     * @return void
     * @throws MerchantEndpointException
     */
    public function sendOrderConfirmedBusinessEvent(OrderConfirmedBusinessEventDto $orderConfirmedBusinessEvent)
    {
        $this->sendBusinessEvent($orderConfirmedBusinessEvent->toArray());
    }

    /**
     * Send merchant_business_event and return 204 no content
     *
     * @param array $eventData
     * @return void
     * @throws MerchantEndpointException
     */
    private function sendBusinessEvent(array $eventData)
    {
        try {
            $request = null;
            $request = $this->createPostRequest(self::BUSINESS_EVENTS_ENDPOINT, $eventData);
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new MerchantEndpointException($e->getMessage(), $request);
        }
        if ($response->isError()) {
            throw new MerchantEndpointException($response->getReasonPhrase(), $request, $response);
        }
    }
}
