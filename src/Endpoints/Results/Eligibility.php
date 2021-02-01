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

namespace Alma\API\Endpoints\Results;

class Eligibility
{
    /**
     * @var bool
     */
    public $isEligible;
    /**
     * @var array
     */
    public $reasons;
    public $constraints;
    /**
     * @var array
     */
    public $paymentPlan;
    /**
     * @var int
     */
    public $installmentsCount;

    /**
     * Eligibility constructor.
     * @param array $data
     * @param int|null $responseCode
     */
    public function __construct($data = [], $responseCode = null)
    {
        // Supporting some legacy behaviour where the eligibility check would return a 406 error if not eligible,
        // instead of 200 OK + {"eligible": false}
        if (array_key_exists('eligible', $data)) {
            $this->setIsEligible($data['eligible']);
        } else {
            $this->setIsEligible($responseCode == 200);
        }

        if (array_key_exists('reasons', $data)) {
            $this->setReasons($data['reasons']);
        }

        if (array_key_exists('constraints', $data)) {
            $this->setConstraints($data['constraints']);
        }

        if (array_key_exists('payment_plan', $data)) {
            $this->setPaymentPlan($data['payment_plan']);
        }

        if (array_key_exists('installments_count', $data)) {
            $this->setInstallmentsCount($data['installments_count']);
        }
    }

    /**
     * Is Eligible
     * @return bool
     */
    public function isEligible()
    {
        return $this->isEligible;
    }

    /**
     * Getter reasons
     * @return string
     */
    public function getReasons()
    {
        return $this->reasons;
    }

    /**
     * Getter constraints
     * @return array
     */
    public function getConstraints()
    {
        return $this->constraints;
    }

    /**
     * Getter paymentPlan
     * @return array
     */
    public function getPaymentPlan()
    {
        return $this->paymentPlan;
    }

    /**
     * Getter paymentPlan
     * @return array
     */
    public function getInstallmentsCount()
    {
        return $this->installmentsCount;
    }

    /**
     * Setter isEligible
     * @param bool $isEligible
     */
    public function setIsEligible($isEligible)
    {
        $this->isEligible = $isEligible;
    }

    /**
     * Setter reasons
     * @param array $reasons
     */
    public function setReasons($reasons)
    {
        $this->reasons = $reasons;
    }

    /**
     * Setter constraints
     * @param array $constraints
     */
    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
    }

    /**
     * Setter paymentPlan
     * @param array $paymentPlan
     */
    public function setPaymentPlan($paymentPlan)
    {
        $this->paymentPlan = $paymentPlan;
    }

    /**
     * Setter paymentPlan
     * @param int $installmentsCount
     */
    public function setInstallmentsCount($installmentsCount)
    {
        $this->installmentsCount = $installmentsCount;
    }
}
