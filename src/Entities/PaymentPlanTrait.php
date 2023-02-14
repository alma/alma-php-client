<?php

namespace Alma\API\Entities;

/**
 * Trait PaymentPlanTrait
 *
 * @package Alma\API\Entities
 */
trait PaymentPlanTrait
{

    /**
     * @return string
     */
    public function getPlanKey()
    {
        return sprintf(
            '%s_%s_%s_%s',
            is_null($this->getKind()) ? '-' : $this->getKind(),
            is_null($this->getInstallmentsCount()) ? '-' : $this->getInstallmentsCount(),
            is_null($this->getDeferredDays()) ? '-' : $this->getDeferredDays(),
            is_null($this->getDeferredMonths()) ? '-' : $this->getDeferredMonths()
        );
    }

    /**
     * Check if a payment plan is "pay later" compliant.
     * Warning, a payment plan can be both un-compliant with "pay later" nor "PnX".
     *
     * @return bool
     */
    public function isPayLaterOnly()
    {
        return 1 === $this->getInstallmentsCount() && ($this->getDeferredDays() || $this->getDeferredMonths());
    }

    /**
     * Check if a payment plan is "PnX" compliant.
     * Warning, a payment plan can be both un-compliant with "pay later" nor "PnX".
     *
     * @return bool
     */
    public function isPnXOnly()
    {
        return $this->getInstallmentsCount() > 1 && (! $this->getDeferredDays() && ! $this->getDeferredMonths());
    }

    /**
     * Check if a payment plan is "PnX" AND "pay later" compliant
     *
     * @return bool
     */
    public function isBothPnxAndPayLater()
    {
        return $this->getInstallmentsCount() > 1 && ($this->getDeferredDays() || $this->getDeferredMonths());
    }

    /**
     * Check if a payment plan is "Pay now" compliant
     *
     * @return bool
     */
    public function isPayNow()
    {
        return $this->getInstallmentsCount() === 1 && (! $this->getDeferredDays() && ! $this->getDeferredMonths());
    }
}
