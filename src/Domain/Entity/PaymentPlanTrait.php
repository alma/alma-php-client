<?php

namespace Alma\API\Domain\Entity;

use Alma\API\Domain\ValueObject\PaymentMethod;

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
    public function getPlanKey(): string
    {
        return sprintf(
            '%s_%s_%s_%s',
            is_null($this->getKind()) ? 'general' : $this->getKind(),
            is_null($this->getInstallmentsCount()) ? '1' : $this->getInstallmentsCount(),
            is_null($this->getDeferredDays()) ? '0' : $this->getDeferredDays(),
            is_null($this->getDeferredMonths()) ? '0' : $this->getDeferredMonths()
        );
    }

    /**
     * Get the payment method this payment plan applies to.
     *
     * @return string
     */
    public function getPaymentMethod(): string
    {
        if ($this->isCredit()) {
            $paymentMethod = PaymentMethod::CREDIT;
        } elseif ($this->isPnXOnly()) {
            $paymentMethod = PaymentMethod::PNX;
        } elseif ($this->isPayLaterOnly()) {
            $paymentMethod = PaymentMethod::PAY_LATER;
        } else {
            $paymentMethod = PaymentMethod::PAY_NOW;
        }
        return $paymentMethod;
    }

    /**
     * Check if a payment plan is "pay later" compliant.
     * Warning, a payment plan can be both un-compliant with "pay later" nor "PnX".
     *
     * @return bool
     */
    public function isPayLaterOnly(): bool
    {
        return 1 === $this->getInstallmentsCount() && ($this->getDeferredDays() || $this->getDeferredMonths());
    }

    /**
     * Check if a payment plan is "PnX" compliant.
     * Warning, a payment plan can be both un-compliant with "pay later" nor "PnX".
     *
     * @return bool
     */
    public function isPnXOnly(): bool
    {
        return $this->getInstallmentsCount() > 1 && $this->getInstallmentsCount() <= 4 && (! $this->getDeferredDays() && ! $this->getDeferredMonths());
    }

    /**
     * Check if a payment plan is "PnX" AND "pay later" compliant
     *
     * @return bool
     */
    public function isBothPnxAndPayLater(): bool
    {
        return $this->getInstallmentsCount() > 1 && ($this->getDeferredDays() || $this->getDeferredMonths());
    }

    /**
     * Check if a payment plan is "Pay now" compliant
     *
     * @return bool
     */
    public function isPayNow(): bool
    {
        return $this->getInstallmentsCount() === 1 && (! $this->getDeferredDays() && ! $this->getDeferredMonths());
    }

    /**
     * Check if a payment plan is "Credit" compliant
     *
     * @return bool
     */
    public function isCredit(): bool
    {
        return $this->getInstallmentsCount() > 4 && (! $this->getDeferredDays() && ! $this->getDeferredMonths());
    }
}
