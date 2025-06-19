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

namespace Alma\API\Entities;

class Eligibility implements PaymentPlanInterface
{
    use PaymentPlanTrait;

    /**
     * @var bool
     */
    public bool $isEligible = false;
    /**
     * @var array
     */
    public array $reasons = [];
    /**
     * @var array
     */
    public array $constraints = [];
    /**
     * @var array
     */
    public array $paymentPlan = [];
    /**
     * @var int
     */
    public int $installmentsCount;
    /**
     *  @var int
     */
    public int $deferredDays;
    /**
     * @var int
     */
    public int $deferredMonths;
    /**
     * @var int
     */
    public int $customerTotalCostAmount = 0;
    /**
     * @var int
     */
    public int $customerTotalCostBps = 0;
    /**
     * @var int|null Percentage of fees + credit in bps paid for by the customer (100bps = 1%)
     *
     * if value is null, that's mean the API does not return this property
     */
    public ?int $annualInterestRate = null;

    /**
     * Eligibility constructor.
     *
     * @param array    $data
     */
    public function __construct(array $data = [])
    {
        if (array_key_exists('eligible', $data)) {
            $this->setIsEligible($data['eligible']);
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

        if (array_key_exists('deferred_days', $data)) {
            $this->setDeferredDays($data['deferred_days']);
        }

        if (array_key_exists('deferred_months', $data)) {
            $this->setDeferredMonths($data['deferred_months']);
        }

        if (array_key_exists('customer_total_cost_amount', $data)) {
            $this->setCustomerTotalCostAmount($data['customer_total_cost_amount']);
        }

        if (array_key_exists('customer_total_cost_bps', $data)) {
            $this->setCustomerTotalCostBps($data['customer_total_cost_bps']);
        }

        if (array_key_exists('annual_interest_rate', $data)) {
            $this->setAnnualInterestRate($data['annual_interest_rate']);
        }
    }

    /**
     * Kind is always 'general' for eligibility at this time
     *
     * @return string
     */
    public function getKind(): string
    {
        return FeePlan::KIND_GENERAL;
    }

    /**
     * Is Eligible.
     *
     * @return bool
     */
    public function isEligible(): bool
    {
        return $this->isEligible;
    }

    /**
     * Getter reasons.
     *
     * @return array
     */
    public function getReasons(): array
    {
        return $this->reasons;
    }

    /**
     * Getter constraints.
     *
     * @return array
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * Getter paymentPlan.
     *
     * @return array
     */
    public function getPaymentPlan(): array
    {
        return $this->paymentPlan;
    }

    /**
     * Getter paymentPlan.
     *
     * @return int
     */
    public function getInstallmentsCount(): int
    {
        return $this->installmentsCount;
    }

    /**
     * Setter isEligible.
     *
     * @param bool $isEligible
     */
    public function setIsEligible(bool $isEligible)
    {
        $this->isEligible = $isEligible;
    }

    /**
     * Setter reasons.
     *
     * @param array $reasons
     */
    public function setReasons(array $reasons)
    {
        $this->reasons = $reasons;
    }

    /**
     * Setter constraints.
     *
     * @param array $constraints
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;
    }

    /**
     * Setter paymentPlan.
     *
     * @param array $paymentPlan
     */
    public function setPaymentPlan(array $paymentPlan)
    {
        $this->paymentPlan = $paymentPlan;
    }

    /**
     * Setter paymentPlan.
     *
     * @param int $installmentsCount
     */
    public function setInstallmentsCount(int $installmentsCount)
    {
        $this->installmentsCount = $installmentsCount;
    }

    /**
     * Get the value of deferredMonths.
     *
     * @return int
     */
    public function getDeferredMonths(): int
    {
        return $this->deferredMonths;
    }

    /**
     * Set the value of deferredMonths.
     *
     * @param mixed $deferredMonths
     */
    public function setDeferredMonths($deferredMonths): Eligibility
    {
        $this->deferredMonths = $deferredMonths;

        return $this;
    }

    /**
     * Get the value of deferredDays.
     *
     * @return int
     */
    public function getDeferredDays(): int
    {
        return $this->deferredDays;
    }

    /**
     * Set the value of deferredDays.
     *
     * @param mixed $deferredDays
     */
    public function setDeferredDays($deferredDays): Eligibility
    {
        $this->deferredDays = $deferredDays;

        return $this;
    }

    /**
     * Get the value of customerTotalCostAmount.
     *
     * @return int
     */
    public function getCustomerTotalCostAmount(): int
    {
        return $this->customerTotalCostAmount;
    }

    /**
     * Set the value of customerTotalCostAmount.
     *
     * @param int $customerTotalCostAmount
     *
     * @return self
     */
    public function setCustomerTotalCostAmount(int $customerTotalCostAmount): Eligibility
    {
        $this->customerTotalCostAmount = $customerTotalCostAmount;

        return $this;
    }

    /**
     * Get the value of customerTotalCostBps.
     *
     * @return int
     */
    public function getCustomerTotalCostBps(): int
    {
        return $this->customerTotalCostBps;
    }

    /**
     * Set the value of customerTotalCostBps.
     *
     * @param int $customerTotalCostBps
     *
     * @return self
     */
    public function setCustomerTotalCostBps(int $customerTotalCostBps): Eligibility
    {
        $this->customerTotalCostBps = $customerTotalCostBps;

        return $this;
    }

    /**
     * Get the value of annualInterestRate.
     * if value is null, that's mean the API does not return this property
     *
     * @return int|null
     */
    public function getAnnualInterestRate(): ?int
    {
        return $this->annualInterestRate;
    }

    /**
     * Set the value of annualInterestRate.
     *
     * @param int $annualInterestRate
     *
     * @return self
     */
    private function setAnnualInterestRate(int $annualInterestRate): Eligibility
    {
        $this->annualInterestRate = $annualInterestRate;

        return $this;
    }

    public function getFeePlanKey(): string
    {
        return implode('_', [FeePlan::KIND_GENERAL, $this->installmentsCount, $this->deferredDays, $this->deferredMonths]);
    }
}
