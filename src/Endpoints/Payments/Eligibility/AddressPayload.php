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

namespace Alma\API\Endpoints\Payments\Eligibility;

use Alma\API\ParamsError;

class AddressPayload
{
    /**
     * Address Payload constructor.
     *
     * @param array    $data
     */
    public function __construct($data)
    {
        $missingAttr = $this->checkMissingMandatoryAttributes($data);
        if ($missingAttr !== false) {
            throw new ParamsError("Invalid Eligibility Request: some mandatory field is missing: <$missingAttr>");
        }

        foreach ($data as $key => $value) {
            switch ($key) {
                case 'title':
                    $this->setTitle($value);
                    break;
                case 'first_name':
                    $this->setFirstName($value);
                    break;
                case 'last_name':
                    $this->setLastName($value);
                    break;
                case 'company':
                    $this->setCompany($value);
                    break;
                case 'line1':
                    $this->setLine1($value);
                    break;
                case 'line2':
                    $this->setLine2($value);
                    break;
                case 'postal_code':
                    $this->setPostalCode($value);
                    break;
                case 'city':
                    $this->setCity($value);
                    break;
                case 'country':
                    $this->setCountry($value);
                    break;
                case 'state_province':
                    $this->setStateProvince($value);
                    break;
                case 'phone':
                    $this->setPhone($value);
                    break;
                default:
                    throw new ParamsError("Invalid Eligibility Request: unknown field <$key>");
                    break;
            }
        }
    }

    private function checkMissingMandatoryAttributes($data): bool|string {
        $mandatoryAttributes = [
            "country",
        ];

        foreach ($mandatoryAttributes as $attr) {
            if (!isset($data[$attr])) {
                return $attr;
            }
        }
        return false;
    }

    public function setTitle($title) {
        $this->title = $title;
    }
    public function setFirstName($firstName) {
        $this->firstName = $firstName;
    }
    public function setLastName($lastName) {
        $this->lastName = $lastName;
    }
    public function setCompany($company) {
        $this->company = $company;
    }
    public function setLine1($line1) {
        $this->line1 = $line1;
    }
    public function setLine2($line2) {
        $this->line2 = $line2;
    }
    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;
    }
    public function setCity($city) {
        $this->city = $city;
    }
    public function setCountry($country) {
        $this->country = $country;
    }
    public function setStateProvince($stateProvince) {
        $this->stateProvince = $stateProvince;
    }
    public function setPhone($phone) {
        $this->phone = $phone;
    }

    public function toPayload() {
        $payload = [
            "country" => $this->country,
        ];
        if (isset($this->title)) {
            $payload['title'] = $this->title;
        }
        if (isset($this->firstName)) {
            $payload['first_name'] = $this->firstName;
        }
        if (isset($this->lastName)) {
            $payload['last_name'] = $this->lastName;
        }
        if (isset($this->company)) {
            $payload['company'] = $this->company;
        }
        if (isset($this->line1)) {
            $payload['line1'] = $this->line1;
        }
        if (isset($this->line2)) {
            $payload['line2'] = $this->line2;
        }
        if (isset($this->postalCode)) {
            $payload['postal_code'] = $this->postalCode;
        }
        if (isset($this->city)) {
            $payload['city'] = $this->city;
        }
        if (isset($this->country)) {
            $payload['country'] = $this->country;
        }
        if (isset($this->stateProvince)) {
            $payload['state_province'] = $this->stateProvince;
        }
        if (isset($this->phone)) {
            $payload['phone'] = $this->phone;
        }
        return $payload;
    }
}

