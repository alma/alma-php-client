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

namespace Alma\API\Entity;

use Alma\API\Exception\ParametersException;

class Eligibility extends AbstractEntity implements PaymentPlanInterface
{
    use PaymentPlanTrait;

    /** @var bool */
    protected bool $isEligible = false;

    /** @var array */
    protected array $reasons = [];

    /** @var array */
    protected array $constraints = [];

    /** @var array */
    protected array $paymentPlan = [];

    /** @var int */
    protected int $installmentsCount;

    /** @var int */
    protected int $deferredDays;

    /** @var int */
    protected int $deferredMonths;

    /** @var int */
    protected int $customerTotalCostAmount = 0;

    /** @var int */
    protected int $customerTotalCostBps = 0;

    /**
     * @var int|null Percentage of fees + credit in bps paid for by the customer (100bps = 1%)
     *
     * if value is null, that's mean the API does not return this property
     */
    protected int $annualInterestRate = 0;

    protected array $requiredFields = [
        'isEligible'              => 'is_eligible',
        'installmentsCount'       => 'installments_count',
        'deferredDays'            => 'deferred_days',
        'deferredMonths'          => 'deferred_months',
    ];

    protected array $optionalFields = [
        'reasons'                 => 'reasons',
        'constraints'             => 'constraints',
        'paymentPlan'             => 'payment_plan',
        'customerTotalCostAmount' => 'customer_total_cost_amount',
        'customerTotalCostBps'    => 'customer_total_cost_bps',
        'annualInterestRate'      => 'annual_interest_rate',
    ];

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
     * Get the value of deferredMonths.
     *
     * @return int
     */
    public function getDeferredMonths(): int
    {
        return $this->deferredMonths;
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
     * Get the value of customerTotalCostAmount.
     *
     * @return int
     */
    public function getCustomerTotalCostAmount(): int
    {
        return $this->customerTotalCostAmount;
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
     * Get the value of annualInterestRate.
     * if value is null, that's mean the API does not return this property
     *
     * @return int|null
     */
    public function getAnnualInterestRate(): ?int
    {
        return $this->annualInterestRate;
    }
}
