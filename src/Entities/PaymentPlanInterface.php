<?php

namespace Alma\API\Entities;

use Alma\API\Endpoints\Results\Eligibility;

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
    public function getDeferredDays();

    /**
     * Get the value of deferredMonths.
     *
     * @return int
     */
    public function getDeferredMonths();

    /**
     * @return string
     */
    public function getPlanKey();

    /**
     * Get the value of installmentsCount.
     *
     * @return int
     */
    public function getInstallmentsCount();

    /**
     * Get the value of kind.
     *
     * @return string
     */
    public function getKind();

    /**
     * Check if a payment plan is "pay later" compliant.
     * Warning, a payment plan can be both un-compliant with "pay later" nor "PnX".
     *
     * @return bool
     */
    public function isPayLaterOnly();

    /**
     * Check if a payment plan is "PnX" compliant.
     * Warning, a payment plan can be both un-compliant with "pay later" nor "PnX".
     *
     * @return bool
     */
    public function isPnXOnly();

    /**
     * Check if a payment plan is "PnX" AND "pay later" compliant.
     *
     * @return bool
     */
    public function isBothPnxAndPayLater();

    /**
     * Check if a payment plan is "Pay Now" compliant.
     *
     * @return bool
     */
    public function isPayNow();
}
