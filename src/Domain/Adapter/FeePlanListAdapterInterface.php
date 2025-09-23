<?php

namespace Alma\API\Domain\Adapter;

use OutOfBoundsException;

interface FeePlanListAdapterInterface
{
    /**
     * Returns a FeePlan by its plan key.
     * @param string $planKey
     * @return FeePlanAdapterInterface
     * @throws OutOfBoundsException if the plan key does not exist in the list.
     */
    public function getByPlanKey(string $planKey): FeePlanAdapterInterface;

    /**
     * Returns a list of Fee Plans that are only available for the given payment method.
     * @param array $paymentMethod
     * @return FeePlanListAdapterInterface
     */
    public function filterFeePlanList(array $paymentMethod): FeePlanListAdapterInterface;

    /**
     * Returns a FeePlanList containing only enabled FeePlans.
     *
     * @return FeePlanListAdapterInterface
     */
    public function filterEnabled(): FeePlanListAdapterInterface;
}
