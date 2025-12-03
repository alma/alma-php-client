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

namespace Alma\API\Domain\Entity;

use Alma\API\Domain\Adapter\FeePlanInterface;

class FeePlan extends AbstractEntity implements PaymentPlanInterface, FeePlanInterface
{
    /**
     * This trait provides methods for handling payment plans, such as getPlanKey.
     */
    use PaymentPlanTrait;

    const KIND_GENERAL = 'general';

    /** @var bool Is this fee plan allowed by Alma? */
    protected bool $allowed;

    /** @var bool Whether this fee plan is available online */
    protected bool $availableOnline;

    /** @var int Percentage of fees in bps paid by the customer (100bps = 1%) */
    protected int $customerFeeVariable;

    /** @var int Number of deferred days this fee plan applies to */
    protected int $deferredDays;

    /** @var int Number of deferred months this fee plan applies to */
    protected int $deferredMonths;

    /** @var int Installments count this fee plan applies to*/
    protected int $installmentsCount;

    /** @var string Kind of payments this fee plan applies to (see kinds above, most likely KIND_GENERAL) */
    protected string $kind;

    /** @var int Maximum purchase amount allowed for this fee plan */
    protected int $maxPurchaseAmount;

    /** @var int Percentage of fees in bps paid for by the merchant (100bps = 1%) */
    protected int $merchantFeeVariable;

    /** @var int Fixed fees in cents paid for by the merchant */
    protected int $merchantFeeFixed;

    /** @var int Minimum purchase amount allowed for this fee plan */
    protected int $minPurchaseAmount;

    /** Mapping of required fields */
    protected array $requiredFields =  [
        'allowed'                      => 'allowed',
        'availableOnline'              => 'available_online',
        'customerFeeVariable'          => 'customer_fee_variable',
        'deferredDays'                 => 'deferred_days',
        'deferredMonths'               => 'deferred_months',
        'installmentsCount'            => 'installments_count',
        'kind'                         => 'kind',
        'maxPurchaseAmount'            => 'max_purchase_amount',
        'merchantFeeVariable'          => 'merchant_fee_variable',
        'merchantFeeFixed'             => 'merchant_fee_fixed',
        'minPurchaseAmount'            => 'min_purchase_amount',
    ];

    /** Mapping of optional fields */
    protected array $optionalFields = [];

    /**
     * Check if this fee plan is:
     * - allowed by Alma.
     * @return bool
     */
    public function isAllowed(): bool {
        return $this->allowed;
    }

    /**
     * Check if this fee plan is eligible for a given purchase amount depends on override.
     * @param int $purchaseAmount Amount in cents
     * @return bool
     */
    public function isEligible(int $purchaseAmount): bool {
        if (!$this->isAvailable()) {
            return false;
        }

        // If the purchase amount is below the minimum override or above the maximum override, it is not eligible
        if (
            $purchaseAmount < $this->getMinPurchaseAmount() ||
            $purchaseAmount > $this->getMaxPurchaseAmount())
        {
            return false;
        }

        return true;
    }

    /**
     * Check if this fee plan is:
     * - allowed by Alma
     * - enabled by the merchant
     * @return bool
     */
    public function isAvailable(): bool {
        return $this->isAllowed();
    }

    /**
     * Check if this fee plan is available online.
     * @return bool True if this fee plan is available online, false otherwise.
     */
    public function isAvailableOnline(): bool {
        return $this->availableOnline;
    }

    /**
     * Get the minimum purchase amount allowed for this fee plan.
     * @return int
     */
    public function getMinPurchaseAmount(): int
    {
        return $this->minPurchaseAmount;
    }

    /**
     * Get the maximum purchase amount allowed for this fee plan.
     * @return int
     */
    public function getMaxPurchaseAmount(): int
    {
        return $this->maxPurchaseAmount;
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
     * Get the number of deferred months this fee plan applies to.
     * @return int The number of deferred months this fee plan applies to.
     */
    public function getDeferredMonths(): int
    {
        return $this->deferredMonths;
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
     * Get the Fixed Merchant Fees applied to this fee plan.
     * @return int
     */
    public function getMerchantFeeFixed(): int
    {
        return $this->merchantFeeFixed;
    }

    /**
     * Get the Variable Merchant Fees applied to this fee plan.
     * @return int
     */
    public function getMerchantFeeVariable(): int
    {
        return $this->merchantFeeVariable;
    }


    /**
     * Get the Variable Customer Fees applied to this fee plan.
     * @return int
     */
    public function getCustomerFeeVariable(): int
    {
        return $this->customerFeeVariable;
    }

    /**
     * Get the kind of payments this fee plan applies to.
     * @return string
     */
    public function getKind(): string
    {
        return self::KIND_GENERAL;
    }

    /**
     * Get the payment method this fee plan applies to.
     * @return string
     */
    public function getLabel(): string {
        if ( $this->isPayNow() ) {
            return 'Pay now';
        } elseif ( $this->isPayLaterOnly() ) {
            return sprintf( '+%d', $this->getDeferredDays() );
        } else {
            return sprintf( '%dx', $this->getInstallmentsCount() );
        }
    }
}
