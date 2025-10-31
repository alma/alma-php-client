<?php

namespace Alma\API\Domain\Adapter;

use OutOfBoundsException;

interface FeePlanListInterface
{
    public function add(FeePlanInterface $feePlan): void;

    public function addList(FeePlanListInterface $feePlanList): void;

    /**
     * Returns a FeePlan by its plan key.
     * @param string $planKey
     * @return FeePlanInterface
     * @throws OutOfBoundsException if the plan key does not exist in the list.
     */
    public function getByPlanKey(string $planKey): FeePlanInterface;

    /**
     * Returns a list of Fee Plans that are only available for the given payment method.
     * @param array $paymentMethod
     * @return FeePlanListInterface
     */
    public function filterFeePlanList(array $paymentMethod): FeePlanListInterface;

    /**
     * Returns a FeePlanList containing only enabled FeePlans.
     *
     * @return FeePlanListInterface
     */
    public function filterEnabled(): FeePlanListInterface;
}
