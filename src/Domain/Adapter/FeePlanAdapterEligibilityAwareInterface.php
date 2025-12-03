<?php

namespace Alma\API\Domain\Adapter;

use Alma\API\Infrastructure\Exception\ParametersException;

interface FeePlanAdapterEligibilityAwareInterface
{
    /**
     * Get the customer total cost amount
     * This Data comes from Eligibility call
     * @return int
     */
    public function getCustomerTotalCostAmount(): int;

    /**
     * Set the customer total cost amount
     * This Data comes from Eligibility call
     *
     * @param int $customerTotalCostAmount
     *
     * @return void
     */
    public function setCustomerTotalCostAmount( int $customerTotalCostAmount );

    /**
     * Get the annual interest rate
     * This Data comes from Eligibility call
     * @return int
     */
    public function getAnnualInterestRate(): int;

    /**
     * Set the annual interest rate
     * This Data comes from Eligibility call
     *
     * @param int $annualInterestRate
     *
     * @return void
     */
    public function setAnnualInterestRate( int $annualInterestRate );

    /**
     * Get the customer total cost bps
     * This Data comes from Eligibility call
     * @return int
     */
    public function getCustomerTotalCostBps(): int;

    /**
     * Set the customer total cost bps
     * This Data comes from Eligibility call
     *
     * @return void
     */
    public function setCustomerTotalCostBps( int $customerTotalCostBps );

    /**
     * Get the customer fee
     * This Data comes from Eligibility call
     * @return int
     */
    public function getCustomerFee(): int;

    /**
     * Set the customer fee
     * This Data comes from Eligibility call
     *
     * @param int $customerFee
     *
     * @return void
     */
    public function setCustomerFee( int $customerFee );

    /**
     * Set the Eligibility.
     * This Data comes from Eligibility call
     *
     * @param bool $eligibility
     *
     * @return void
     */
    public function setEligibility( bool $eligibility ): void;

    /**
     * Define if the Fee Plan is Eligible or not.
     * It must be Eligible, Available and in the boundaries.
     * Override the default method to check the min and max overrides.
     *
     * @param int|null $purchaseAmount
     *
     * @return bool
     */
    public function isEligible( ?int $purchaseAmount = null ): bool;

    /**
     * Return the PaymentPlan
     * This Data comes from Eligibility call
     * @return array
     */
    public function getPaymentPlan(): array;

    /**
     * Add Payment Plans to Fee Plans
     * This Data comes from Eligibility call
     *
     * @param array $paymentPlan
     *
     * @return void
     */
    public function setPaymentPlan( array $paymentPlan ): void;
}
