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

class EligibilityPayload extends Payload
{
    /**
     * Eligibility Payload constructor.
     *
     * @param array $data
     */
    public function __construct($data)
    {
        $missingAttr = $this->checkMissingMandatoryAttributes($data, ['purchase_amount', 'queries']);
        if ($missingAttr !== null) {
            throw new ParamsError("Invalid Eligibility Request: some mandatory field is missing: <$missingAttr>");
        }

        foreach ($data as $key => $value) {
            switch ($key) {
                case 'purchase_amount':
                    $this->setPurchaseAmount($value);
                    break;
                case 'queries':
                    $this->setQueries($value);
                    break;
                case 'billing_address':
                    $this->setBillingAddress($value);
                    break;
                case 'shipping_address':
                    $this->setShippingAddress($value);
                    break;
                case 'locale':
                    $this->setLocale($value);
                    break;

                default:
                    throw new ParamsError("Invalid Eligibility Request: unknown field <$key>");
                    break;
            }
        }
    }

    public function setPurchaseAmount($amount) {
        $this->purchaseAmount = $amount;
    }

    public function setQueries(array $queries) {
        if (empty($queries)) {
            throw new ParamsError("Invalid Eligibility Request: queries is empty");
        }

        $this->queries = [];
        foreach ($queries as $query) {
            $queryPayload = new QueryPayload($query);
            $this->queries[] = $queryPayload->toPayload();
        }
    }

    public function setBillingAddress(array $address) {
        $this->billingAddress = new AddressPayload($address);
    }

    public function setShippingAddress(array $address) {
        $this->shippingAddress = new AddressPayload($address);
    }

    public function setLocale(string $locale) {
        $this->locale = $locale;
    }

    public function toPayload() {
        $payload = [
            'purchase_amount' => $this->purchaseAmount,
            'queries' => $this->queries
        ];
        if (isset($this->billingAddress)) {
            $payload['billing_address'] = $this->billingAddress->toPayload();
        }
        if (isset($this->shippingAddress)) {
            $payload['shipping_address'] = $this->shippingAddress->toPayload();
        }
        if (isset($this->locale)) {
            $payload['locale'] = $this->locale;
        }
        return $payload;
    }
}
