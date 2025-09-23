<?php

namespace Alma\API\Domain\Adapter;

use Alma\API\Infrastructure\Exception\ParametersException;

interface FeePlanInterface {

    /**
     * Check if this fee plan is allowed by Alma.
     * @return bool
     */
    public function isAllowed(): bool;

    /**
     * Check if this fee plan is eligible for a given purchase amount depends on override.
     * @param int $purchaseAmount Amount in cents
     * @return bool
     */
    public function isEligible(int $purchaseAmount): bool;

    /**
     * Check if this fee plan is:
     * - enabled by the merchant.
     * @return bool
     */
    public function isEnabled(): bool;

    /**
     * Enable this fee plan.
     * @return void
     */
    public function enable() : void;

    /**
     * Check if this fee plan is:
     * - allowed by Alma
     * - enabled by the merchant
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * Check if this fee plan is available online.
     * @return bool True if this fee plan is available online, false otherwise.
     */
    public function isAvailableOnline(): bool;

    /**
     * Get the minimum purchase amount allowed for this fee plan.
     * @return int
     */
    public function getMinPurchaseAmount(): int;

    /**
     * Get the maximum purchase amount allowed for this fee plan.
     * @return int
     */
    public function getMaxPurchaseAmount(): int;

    /**
     * Get the number of deferred days this fee plan applies to.
     * @return int The number of deferred days this fee plan applies to.
     */
    public function getDeferredDays(): int;

    /**
     * Get the number of deferred months this fee plan applies to.
     * @return int The number of deferred months this fee plan applies to.
     */
    public function getDeferredMonths(): int;

    /**
     * Get the installments count this fee plan applies to.
     * @return int
     */
    public function getInstallmentsCount(): int;

    /**
     * Get the Fixed Merchant Fees applied to this fee plan.
     * @return int|null
     */
    public function getMerchantFeeFixed(): ?int;

    /**
     * Get the Variable Merchant Fees applied to this fee plan.
     * @return int|null
     */
    public function getMerchantFeeVariable(): ?int;


    /**
     * Get the Variable Customer Fees applied to this fee plan.
     * @return int|null
     */
    public function getCustomerFeeVariable(): ?int;

    /**
     * Get the kind of payments this fee plan applies to.
     * @return string
     */
    public function getKind(): string;
}
