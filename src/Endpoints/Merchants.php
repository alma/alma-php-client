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

use Alma\API\Entities\DTO\MerchantBusinessEvent\CartInitiatedBusinessEvent;
use Alma\API\Entities\DTO\MerchantBusinessEvent\OrderConfirmedBusinessEvent;
use Alma\API\Entities\FeePlan;
use Alma\API\Entities\Merchant;
use Alma\API\Exceptions\RequestException;
use Alma\API\RequestError;

class Merchants extends Base
{
    const ME_PATH = '/v1/me';

    /**
     * @return Merchant
     * @throws RequestError
     */
    public function me()
    {
        $res = $this->request(self::ME_PATH . '/extended-data')->get();

        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }

        return new Merchant($res->json);
    }

    /**
     * @param $kind string  Either FeePlan::KIND_GENERAL or FeePlan::KIND_POS. The former is applied to online payments,
     *                      while the latter will be used when creating a Payment with origin=pos_* for
     *                      retail/point-of-sale use cases. Defaults to FeePlan::KIND_GENERAL
     * @param string|int[] $installmentsCounts Only include fee plans that match the given installments counts, or use
     *                                         the string "all" (default) to get all available fee plans
     * @param bool $includeDeferred Include deferred fee plans (i.e. Pay Later plans) in the response
     * @return FeePlan[] An array of available fee plans (some might be disabled, check FeePlan->allowed for each)
     * @throws RequestError
     */
    public function feePlans($kind = FeePlan::KIND_GENERAL, $installmentsCounts = "all", $includeDeferred = false)
    {
        if (is_array($installmentsCounts)) {
            $only = implode(",", $installmentsCounts);
        } else {
            $only = $installmentsCounts;
        }

        $res = $this->request(self::ME_PATH . "/fee-plans")->setQueryParams(array(
            "kind" => $kind,
            "only" => $only,
            "deferred" => $includeDeferred ? "true" : "false" // Avoid conversion to "0"/"1" our API doesn't recognize
        ))->get();

        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }

        return array_map(function ($val) {
            return new FeePlan($val);
        }, $res->json);
    }

    /**
     * Prepare and send a business event for a cart initiated
     *
     * @param CartInitiatedBusinessEvent $cartEventData
     * @return void
     * @throws RequestException
     */
    public function sendCartInitiatedBusinessEvent(CartInitiatedBusinessEvent $cartEventData)
    {
        $cartEventDataPayload = [
            'event_type' => $cartEventData->getEventType(),
            'cart_id' => $cartEventData->getCartId()
        ];
        $this->sendBusinessEvent($cartEventDataPayload);
    }

    /**
     * Prepare and send a business event for Order confirmed
     *
     * @param OrderConfirmedBusinessEvent $orderConfirmedBusinessEvent
     * @return void
     * @throws RequestException
     */
    public function sendOrderConfirmedBusinessEvent(OrderConfirmedBusinessEvent $orderConfirmedBusinessEvent)
    {
        $cartEventDataPayload = [
            'event_type' => $orderConfirmedBusinessEvent->getEventType(),
            'is_alma_p1x' => $orderConfirmedBusinessEvent->isAlmaP1X(),
            'is_alma_bnpl' => $orderConfirmedBusinessEvent->isAlmaBNPL(),
            'was_bnpl_eligible' => $orderConfirmedBusinessEvent->wasBNPLEligible(),
            'order_id' => $orderConfirmedBusinessEvent->getOrderId(),
            'cart_id' => $orderConfirmedBusinessEvent->getCartId(),
            'alma_payment_id' => $orderConfirmedBusinessEvent->getAlmaPaymentId()
        ];
        $this->sendBusinessEvent($cartEventDataPayload);
    }

    /**
     * Send merchant_business_event and return 204 no content
     *
     * @param array $eventData
     * @return void
     * @throws RequestException
     */
    private function sendBusinessEvent($eventData)
    {
        try {
            $res = $this->request(self::ME_PATH . "/business-events")->setRequestBody($eventData)->post();
        } catch (RequestError $e) {
            throw new RequestException($e->getErrorMessage(), null);
        }
        if ($res->isError()) {
            throw new RequestException($res->errorMessage, null, $res);
        }
    }

}
