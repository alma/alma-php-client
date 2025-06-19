<?php

namespace Alma\API\Entities;

use ArrayObject;
use OutOfBoundsException;

class FeePlanList extends ArrayObject
{
    public function __construct($array = [], $flags = 0, $iteratorClass = "ArrayIterator")
    {
        parent::__construct($array, $flags, $iteratorClass);
    }

    public function add(FeePlan $feePlan): void
    {
        $this[] = $feePlan;
    }

    /**
     * Returns a FeePlan by its plan key.
     * @param string $planKey
     * @return FeePlan
     * @throws OutOfBoundsException if the plan key does not exist in the list.
     */
    public function getByPlanKey(string $planKey): FeePlan
    {
        $filter = array_values(array_filter($this->getArrayCopy(), function($feePlan) use ($planKey) {
            return $feePlan->getPlanKey() === $planKey;
        }));
        return $filter[0];
    }

    /**
     * Returns a list of Fee Plans that are only available for the given payment method.
     * @param $paymentMethod
     * @return FeePlanList
     */
    public function filterFeePlanList($paymentMethod): FeePlanList
    {
        switch ($paymentMethod) {
            case 'credit':
                $feePlanList = new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
                    return $feePlan->isCredit();
                })));
                break;
            case 'pnx':
                $feePlanList = new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
                    return $feePlan->isPnXOnly();
                })));
                break;
            case 'pay-later':
                $feePlanList = new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
                    return $feePlan->isPayLaterOnly();
                })));
                break;
            case 'pay-now':
                $feePlanList = new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
                    return $feePlan->isPayNow();
                })));
                break;
            default:
                $feePlanList = new FeePlanList();
        }

        return $feePlanList;
    }
}
