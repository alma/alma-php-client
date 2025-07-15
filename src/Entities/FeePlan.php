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

use InvalidArgumentException;

class FeePlan implements PaymentPlanInterface
{
    /**
     * This trait provides methods for handling payment plans, such as getPlanKey.
     */
    use PaymentPlanTrait;

    const KIND_GENERAL = 'general';
    const KIND_POS = 'pos';

    /** @var ?bool Is this fee plan enabled by merchant? True by default */
    protected bool $enabled = true;

    /** @var ?bool Is this fee plan available? True by default, merchant rules can make it unavailable */
    protected bool $available = true;

    /** @var bool Is this fee plan allowed by Alma? */
    protected bool $allowed = false;

    /** @var bool Whether this fee plan is available in POS (point of sale) */
    protected bool $availableInPos = false;

    /** @var bool Whether this fee plan is available online */
    protected bool $availableOnline = false;

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

    /** @var mixed|null Local override of the maximum purchase amount allowed for this fee plan */
    private $overrideMaxPurchaseAmount;

    /** @var ?string */
    protected ?string $merchant;

    /** @var ?int Percentage of fees in bps paid for by the merchant (100bps = 1%) */
    protected ?int $merchantFeeVariable;

    /** @var ?int Fixed fees in cents paid for by the merchant */
    protected ?int $merchantFeeFixed;

    /** @var ?int Minimum purchase amount allowed for this fee plan */
    protected ?int $minPurchaseAmount;

    /** @var mixed|null Local override of the minimum purchase amount allowed for this fee plan */
    private $overrideMinPurchaseAmount;

    /** @var ?bool Whether payout is made on acceptance of the payment plan */
    protected ?bool $payoutOnAcceptance;

    public function __construct(array $attributes) {
        $this->enabled                      = $attributes['enabled'] ?? true;
        $this->available                    = $attributes['available'] ?? true;
        $this->allowed                      = $attributes['allowed'] ?? false;
        $this->availableInPos               = $attributes['available_in_pos'] ?? false;
        $this->availableOnline              = $attributes['available_online'] ?? false;
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
        $this->overrideMaxPurchaseAmount    = $attributes['override_max_purchase_amount'] ?? null;
        $this->merchant                     = $attributes['merchant'] ?? null;
        $this->merchantFeeVariable          = $attributes['merchant_fee_variable'] ?? null;
        $this->merchantFeeFixed             = $attributes['merchant_fee_fixed'] ?? null;
        $this->minPurchaseAmount            = $attributes['min_purchase_amount'] ?? null;
        $this->overrideMinPurchaseAmount    = $attributes['override_min_purchase_amount'] ?? null;
        $this->payoutOnAcceptance           = $attributes['payout_on_acceptance'] ?? null;
    }

    /**
     * Check if this fee plan is:
     * - allowed by Alma.
     * @return bool
     */
    public function isAllowed(): bool {
        return $this->allowed;
    }

    public function isEligible($purchaseAmount): bool {
        if (!$this->isAvailable()) {
            return false;
        }

        // If the purchase amount is below the minimum or above the maximum, it is not eligible
        if ($purchaseAmount < $this->getMinPurchaseAmount(true) || $purchaseAmount > $this->getMaxPurchaseAmount(true)) {
            return false;
        }

        return true;
    }

    /**
     * Check if this fee plan is:
     * - allowed by Alma
     * - enabled by the merchant.
     * @return bool
     */
    public function isEnabled(): bool {
        return $this->isAllowed() && $this->enabled;
    }

    /**
     * Disable this fee plan.
     * @return void
     */
    public function disable() : void {
        $this->enabled = false;
    }

    /**
     * Check if this fee plan is:
     * - allowed by Alma
     * - enabled by the merchant
     * - available (not disabled by contextual rules)
     * @return bool
     */
    public function isAvailable(): bool {
        return $this->isEnabled() && $this->available;
    }

    public function makeUnavailable() : void {
        $this->available = false;
    }

    /**
     * Check if this fee plan is available in POS (point of sale).
     * @return bool True if this fee plan is available in POS, false otherwise.
     */
    public function get_available_in_pos(): bool {
        return $this->availableInPos;
    }

    /**
     * Check if this fee plan is available online.
     * @return bool True if this fee plan is available online, false otherwise.
     */
    public function get_available_online(): bool {
        return $this->availableOnline;
    }

    /**
     * Get the minimum purchase amount allowed for this fee plan.
     * @param bool $localOverride If true, returns the local override if set, otherwise returns the minimum amount given by API.
     * @return ?int
     */
    public function getMinPurchaseAmount(bool $localOverride = false): int
    {
        if ($localOverride) {
            return $this->overrideMinPurchaseAmount ?? $this->minPurchaseAmount;
        }
        return $this->minPurchaseAmount;
    }

    /**
     * Set a local override to the minimum purchase amount allowed for this fee plan.
     * @param int $overrideMinPurchaseAmount Amount in cents
     * @return void
     */
    public function setOverrideMinPurchaseAmount(int $overrideMinPurchaseAmount): void
    {
        if ($overrideMinPurchaseAmount < $this->minPurchaseAmount) {
	        return; // No need to throw an exception, just ignore the override
	        // throw new InvalidArgumentException("Override minimum purchase amount must be higher than the minimum amount given by API.");
        }
        $this->overrideMinPurchaseAmount = $overrideMinPurchaseAmount;
    }

    /**
     * Get the maximum purchase amount allowed for this fee plan.
     * @param bool $localOverride If true, returns the local override if set, otherwise returns the maximum amount given by API.
     * @return ?int
     */
    public function getMaxPurchaseAmount(bool $localOverride = false): int
    {
        if ($localOverride) {
            return $this->overrideMaxPurchaseAmount ?? $this->maxPurchaseAmount;
        }
        return $this->maxPurchaseAmount;
    }

    /**
     * Set a local override to the maximum purchase amount allowed for this fee plan.
     * @param int $overrideMaxPurchaseAmount Amount in cents
     * @return void
     */
    public function setOverrideMaxPurchaseAmount(int $overrideMaxPurchaseAmount): void
    {
        if ($overrideMaxPurchaseAmount > $this->maxPurchaseAmount) {
            return; // No need to throw an exception, just ignore the override
            // throw new InvalidArgumentException("Override maximum purchase amount must be lower than the maximum amount given by API.");
        }
        $this->overrideMaxPurchaseAmount = $overrideMaxPurchaseAmount;
    }

    /**
     * Get the number of deferred days this fee plan applies to.
     * @return int The number of deferred days this fee plan applies to.
     */
    public function getDeferredDays(): int
    {
        return $this->deferredDays;
    }

    /**
     * Set the number of deferred days this fee plan applies to.
     * @param $deferredDays int The number of deferred days to set.
     * @return self
     */
    public function setDeferredDays(int $deferredDays): self
    {
        $this->deferredDays = $deferredDays;
        return $this;
    }

    /**
     * Get the number of deferred months this fee plan applies to.
     * @return int The number of deferred months this fee plan applies to.
     */
    public function getDeferredMonths(): int
    {
        return $this->deferredMonths;
    }

    /**
     * Set the number of deferred months this fee plan applies to.
     * @param $deferredMonths int The number of deferred months to set.
     * @return self
     */
    public function setDeferredMonths(int $deferredMonths): self
    {
        $this->deferredMonths = $deferredMonths;
        return $this;
    }

    /**
     * Get the deferred trigger limit days.
     * @return int
     */
    public function getDeferredTriggerLimitDays(): int
    {
        return $this->deferredTriggerLimitDays;
    }

    /**
     * Get the installments count this fee plan applies to.
     * @return int
     */
    public function getInstallmentsCount(): int
    {
        return $this->installmentsCount;
    }

    /**
     * Set the installments count this fee plan applies to.
     * @param $installmentsCount int The number of installments to set.
     * @return $this
     */
    public function setInstallmentsCount(int $installmentsCount): self
    {
        $this->installmentsCount = $installmentsCount;
        return $this;
    }

    /**
     * Get the Fixed Merchant Fees applied to this fee plan.
     * @return int|null
     */
    public function getMerchantFeeFixed(): ?int
    {
        return $this->merchantFeeFixed;
    }

    /**
     * Get the Variable Merchant Fees applied to this fee plan.
     * @return int|null
     */
    public function getMerchantFeeVariable(): ?int
    {
        return $this->merchantFeeVariable;
    }

    /**
     * Get the Fixed Customer Fees applied to this fee plan.
     * @return int|null
     */
    public function getCustomerFeeFixed(): ?int
    {
        return $this->customerFeeFixed;
    }

    /**
     * Get the Variable Customer Fees applied to this fee plan.
     * @return int|null
     */
    public function getCustomerFeeVariable(): ?int
    {
        return $this->customerFeeVariable;
    }

    /**
     * Get the Customer Lending Rate applied to this fee plan.
     * @return int|null
     */
    public function getCustomerLendingRate(): ?int
    {
        return $this->customerLendingRate;
    }

    /**
     * Get the kind of payments this fee plan applies to.
     * @return string
     */
    public function getKind(): string
    {
        return $this->kind;
    }
}
