<?php
/**
 * Copyright (c) 2018 Alma / Nabla SAS.
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

use Alma\API\ParamsError;
use Alma\API\Services\AbstractPayload;

class QueryPayload extends AbstractPayload
{
    private $minPurchaseAmount;
    private $maxPurchaseAmount;
    private $installmentsCount;
    private $deferredTriggerLimitDays;
    private $deferredDays;
    private $allowed;

    /**
     * Query PayloadInterface constructor.
     *
     * @param array $data
     *
     * @throws ParamsError
     */
    public function __construct($data)
    {
        if (empty($data)) {
            return;
        }
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'allowed':
                    $this->setAllowed($value);
                    break;
                case 'deferred_days':
                    $this->setDeferredDays($value);
                    break;
                case 'deferred_trigger_limit_days':
                    $this->setDeferredTriggerLimitDays($value);
                    break;
                case 'installments_count':
                    $this->setInstallmentsCount($value);
                    break;
                case 'max_purchase_amount':
                    $this->setMaxPurchaseAmount($value);
                    break;
                case 'min_purchase_amount':
                    $this->setMinPurchaseAmount($value);
                    break;
                default:
                    throw new ParamsError("Invalid Eligibility Request: unknown field <$key>");
            }
        }
    }

    public function setAllowed($allowed) {
        $this->allowed = $allowed;
    }

    public function setDeferredDays($deferredDays) {
        $this->deferredDays = $deferredDays;
    }

    public function setDeferredTriggerLimitDays($deferredTriggerLimitDays) {
        $this->deferredTriggerLimitDays = $deferredTriggerLimitDays;
    }

    public function setInstallmentsCount($installmentsCount) {
        $this->installmentsCount = $installmentsCount;
    }

    public function setMaxPurchaseAmount($maxPurchaseAmount) {
        $this->maxPurchaseAmount = $maxPurchaseAmount;
    }

    public function setMinPurchaseAmount($minPurchaseAmount) {
        $this->minPurchaseAmount = $minPurchaseAmount;
    }

    /**
     * @return bool
     * @throws ParamsError
     */
    public function validate() {
        if (empty($this->queries)) {
            throw new ParamsError("Invalid Eligibility Request: queries is empty");
        }

        if (!isset($this->installments_count)) {
            throw new ParamsError("Invalid Eligibility Request: some mandatory field is missing: <installments_count>");
        }
        return true;
    }

    public function toPayload() {
        $payload = [
            "installments_count" => $this->installmentsCount,
        ];
        if (isset($this->allowed)) {
            $payload['allowed'] = $this->allowed;
        }
        if (isset($this->deferredDays)) {
            $payload['deferred_days'] = $this->deferredDays;
        }
        if (isset($this->deferredTriggerLimitDays)) {
            $payload["deferred_trigger_limit_days"] = $this->deferredTriggerLimitDays;
        }
        if (isset($this->maxPurchaseAmount)) {
            $payload["max_purchase_amount"] = $this->maxPurchaseAmount;
        }
        if (isset($this->minPurchaseAmount)) {
            $payload["min_purchase_amount"] = $this->minPurchaseAmount;
        }
        return $payload;
    }
}

