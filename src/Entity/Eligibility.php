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

/**
 * Class Eligibility
 * @package Alma\API\Entity
 *
 * @link https://docs.almapay.com/reference/verifier-eligibilite-achat
 */
class Eligibility extends AbstractEntity implements PaymentPlanInterface
{
    use PaymentPlanTrait;

    /** @var bool Tells whether the installment is eligible (true) or not (false). */
    protected bool $isEligible = false;

    /** @var int Number of deferred days for a deferred payment. */
    protected int $deferredDays = 0;

    /** @var int Number of deferred months for a deferred payment. */
    protected int $deferredMonths = 0;

    /** @var int Number of installments in the installment plan (3 by default). */
    protected int $installmentsCount = 3;

    /**
     * @var int Total amount of fees and interest paid by the client in cents.
     * Interest is calculated based on the date of the eligibility call.
     */
    protected int $customerTotalCostAmount = 0;

    /**
     * @var int Percentage in bps of the share of fees and interest paid by the client.
     * Interest is calculated based on the date of the eligibility call.
     * - For pay-in-3 and pay-in-4, this most often corresponds to the customer_fee_variable.
     * - For credit (more than 4 installments) this value changes based on the calculation of interest
     * and therefore the start date of the schedule. It has an informative value but is not contractual.
     * It is therefore not recommended to display it in the payment journey.
     */
    protected int $customerTotalCostBps = 0;

    /** @var array List of installments for this purchase. This field is available only when eligibility value is true. */
    protected array $paymentPlan = [];

    /** @var array Constraints that the request fails to satisfy, causing the ineligibility */
    protected array $constraints = [];

    /** @var array Reason for ineligibility */
    protected array $reasons = [];

    /** Mapping of required fields */
    protected array $requiredFields = [
        'isEligible'              => 'eligible',
        'deferredDays'            => 'deferred_days',
        'deferredMonths'          => 'deferred_months',
        'installmentsCount'       => 'installments_count',
        'customerTotalCostAmount' => 'customer_total_cost_amount',
        'customerTotalCostBps'    => 'customer_total_cost_bps',
        'paymentPlan'             => 'payment_plan',
    ];

    /** Mapping of optional fields */
    protected array $optionalFields = [
        'constraints'             => 'constraints',
        'reasons'                 => 'reasons',
    ];

    /**
     * Kind is always 'general' for eligibility at this time
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getKind(): string
    {
        return FeePlan::KIND_GENERAL;
    }

    /**
     * Tells whether the installment is eligible (true) or not (false).
     * @return bool
     * @noinspection PhpUnused Used by implementations
     */
    public function isEligible(): bool
    {
        return $this->isEligible;
    }

    /**
     * Get the number of deferred days for a deferred payment.
     * @return int
     * @noinspection PhpUnused Used by implementations
     */
    public function getDeferredDays(): int
    {
        return $this->deferredDays;
    }

    /**
     * Get the number of deferred months for a deferred payment.
     * @return int
     * @noinspection PhpUnused Used by implementations
     */
    public function getDeferredMonths(): int
    {
        return $this->deferredMonths;
    }

    /**
     * Get the number of installments in the installment plan.
     * @return int
     * @noinspection PhpUnused Used by implementations
     */
    public function getInstallmentsCount(): int
    {
        return $this->installmentsCount;
    }

    /**
     * Get the total amount of fees and interest paid by the client in cents.
     * @return int
     * @noinspection PhpUnused Used by implementations
     */
    public function getCustomerTotalCostAmount(): int
    {
        return $this->customerTotalCostAmount;
    }

    /**
     * Get the percentage in bps of the share of fees and interest paid by the client.
     * @return int
     * @noinspection PhpUnused Used by implementations
     */
    public function getCustomerTotalCostBps(): int
    {
        return $this->customerTotalCostBps;
    }

    /**
     * Get Payment Plans
     * @return array
     * @noinspection PhpUnused Used by implementations
     */
    public function getPaymentPlan(): array
    {
        return $this->paymentPlan;
    }

    /**
     * Get failure constraints.
     * @return array
     * @noinspection PhpUnused Used by implementations
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * Get failure reasons.
     * @return array
     * @noinspection PhpUnused Used by implementations
     */
    public function getReasons(): array
    {
        return $this->reasons;
    }
}
