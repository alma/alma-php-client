<?php

namespace Alma\API\Entity;

/**
 * Interface PaymentPlanInterface
 *
 * @package Alma\API\Entities
 */
interface PaymentPlanInterface
{
    /**
     * Get the value of deferredDays.
     *
     * @return int
     */
    public function getDeferredDays(): int;

    /**
     * Get the value of deferredMonths.
     *
     * @return int
     */
    public function getDeferredMonths(): int;

    /**
     * @return string
     */
    public function getPlanKey(): string;

    /**
     * Get the value of installmentsCount.
     *
     * @return int
     */
    public function getInstallmentsCount(): int;

    /**
     * Get the value of kind.
     *
     * @return string
     */
    public function getKind(): string;

    /**
     * Check if a payment plan is "pay later" compliant.
     * Warning, a payment plan can be both un-compliant with "pay later" nor "PnX".
     *
     * @return bool
     */
    public function isPayLaterOnly(): bool;

    /**
     * Check if a payment plan is "PnX" compliant.
     * Warning, a payment plan can be both un-compliant with "pay later" nor "PnX".
     *
     * @return bool
     */
    public function isPnXOnly(): bool;

    /**
     * Check if a payment plan is "PnX" AND "pay later" compliant.
     *
     * @return bool
     */
    public function isBothPnxAndPayLater(): bool;

    /**
     * Check if a payment plan is "Pay Now" compliant.
     *
     * @return bool
     */
    public function isPayNow(): bool;
}
