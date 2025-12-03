<?php

namespace Alma\API\Domain\Adapter;

use OutOfBoundsException;

interface FeePlanListAdapterInterface
{
    /**
     * Add a FeePlan to the FeePlanList.
     *
     * @param FeePlanAdapterInterface $feePlanAdapter
     * @return void
     */
    public function add(FeePlanAdapterInterface $feePlanAdapter): void;

    /**
     * Add a list of FeePlans to the FeePlanList.
     *
     * @param FeePlanListAdapterInterface $feePlanListAdapter
     * @return void
     */
    public function addList(FeePlanListAdapterInterface $feePlanListAdapter): void;

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
