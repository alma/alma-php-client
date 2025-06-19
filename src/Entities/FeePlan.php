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

namespace Alma\API\Entities;

class FeePlan implements PaymentPlanInterface
{
    use PaymentPlanTrait;

    const KIND_GENERAL = 'general';
    const KIND_POS = 'pos';

    /** @var ?bool Is this fee plan allowed/enabled? */
    protected ?bool $allowed;

    /** @var ?bool Whether this fee plan is available in POS (point of sale) */
    protected ?bool $availableInPos;

    /** @var ?bool Whether this fee plan is available online */
    protected ?bool $availableOnline;

    /** @var ?int Fixed fees in cents paid by the customer */
    protected ?int $customerFeeFixed;

    /** @var ?int Percentage of fees in bps paid by the customer (100bps = 1%) */
    protected ?int $customerFeeVariable;

    /** @var ?int Percentage of lending rate in bps used to calculate the fee plan interest paid by the customer (100bps = 1%) */
    protected ?int $customerLendingRate;

    /** @var int Number of deferred days this fee plan applies to */
    protected int $deferredDays = 0;

    /** @var int Number of deferred months this fee plan applies to */
    protected int $deferredMonths = 0;

    /** @var ?bool Whether this fee plan bypasses scoring for deferred triggers */
    protected ?bool $deferredTriggerBypassScoring;

    /** @var ?int Number of deferred trigger limit days this fee plan applies to */
    protected ?int $deferredTriggerLimitDays;

    protected ?int $firstInstallmentRatio;

    /** @var ?int Numeric identifier */
    private ?int $id;

    /** @var int Installments count this fee plan applies to*/
    protected int $installmentsCount = 1;

    /** @var string Kind of payments this fee plan applies to (see kinds above, most likely KIND_GENERAL) */
    protected string $kind;

    /** @var ?int Maximum purchase amount allowed for this fee plan */
    protected ?int $maxPurchaseAmount;

    /** @var ?string */
    protected ?string $merchant;

    /** @var ?int Percentage of fees in bps paid for by the merchant (100bps = 1%) */
    protected ?int $merchantFeeVariable;

    /** @var ?int Fixed fees in cents paid for by the merchant */
    protected ?int $merchantFeeFixed;

    /** @var ?int Minimum purchase amount allowed for this fee plan */
    protected ?int $minPurchaseAmount;

    /** @var ?bool Whether payout is made on acceptance of the payment plan */
    protected ?bool $payoutOnAcceptance;

    public function __construct(array $attributes) {
        $this->allowed                      = $attributes['allowed'] ?? null;
        $this->availableInPos               = $attributes['available_in_pos'] ?? null;
        $this->availableOnline              = $attributes['available_online'] ?? null;
        $this->customerFeeFixed             = $attributes['customer_fee_fixed'] ?? null;
        $this->customerFeeVariable          = $attributes['customer_fee_variable'] ?? null;
        $this->customerLendingRate          = $attributes['customer_lending_rate'] ?? null;
        $this->deferredDays                 = $attributes['deferred_days'] ?? 0;
        $this->deferredMonths               = $attributes['deferred_months'] ?? 0;
        $this->deferredTriggerBypassScoring = $attributes['deferred_trigger_bypass_scoring'] ?? null;
        $this->deferredTriggerLimitDays     = $attributes['deferred_trigger_limit_days'] ?? null;
        $this->firstInstallmentRatio        = $attributes['first_installment_ratio'] ?? null;
        $this->id                           = $attributes['id'] ?? null;
        $this->installmentsCount            = $attributes['installments_count'] ?? 1;
        $this->kind                         = $attributes['kind'] ?? 'general';
        $this->maxPurchaseAmount            = $attributes['max_purchase_amount'] ?? null;
        $this->merchant                     = $attributes['merchant'] ?? null;
        $this->merchantFeeVariable          = $attributes['merchant_fee_variable'] ?? null;
        $this->merchantFeeFixed             = $attributes['merchant_fee_fixed'] ?? null;
        $this->minPurchaseAmount            = $attributes['min_purchase_amount'] ?? null;
        $this->payoutOnAcceptance           = $attributes['payout_on_acceptance'] ?? null;
    }

    /**
     * @return ?int
     */
    public function getMaxPurchaseAmount(): ?int
    {
        return $this->maxPurchaseAmount;
    }

    public function getDeferredDays(): int
    {
        return $this->deferredDays;
    }

    public function setDeferredDays($deferredDays): self
    {
        $this->deferredDays = $deferredDays;
        return $this;
    }

    public function getDeferredMonths(): int
    {
        return $this->deferredMonths;
    }

    public function setDeferredMonths($deferredMonths): self
    {
        $this->deferredMonths = $deferredMonths;
        return $this;
    }

    public function getDeferredTriggerLimitDays(): int
    {
        return $this->deferredTriggerLimitDays;
    }

    public function getInstallmentsCount(): int
    {
        return $this->installmentsCount;
    }

    public function setInstallmentsCount($installmentsCount): self
    {
        $this->installmentsCount = $installmentsCount;
        return $this;
    }

    public function getKind(): string
    {
        return $this->kind;
    }
}
