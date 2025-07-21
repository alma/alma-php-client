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

	public function addList(FeePlanList $feePlanList): void
	{
		foreach ($feePlanList as $feePlan) {
			$this->add($feePlan);
		}
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
     * @param array $paymentMethod
     * @return FeePlanList
     */
    public function filterFeePlanList(array $paymentMethod): FeePlanList
    {
	    $feePlanList = new FeePlanList();
        if (in_array('credit', $paymentMethod)) {
	        $feePlanList->addList(new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
                return $feePlan->isCredit();
            }))));
        }
	    if (in_array('pnx', $paymentMethod)) {
		    $feePlanList->addList(new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
			    return $feePlan->isPnXOnly();
		    }))));
	    }
	    if (in_array('pay-later', $paymentMethod)) {
		    $feePlanList->addList(new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
			    return $feePlan->isPayLaterOnly();
		    }))));
	    }
	    if (in_array('pay-now', $paymentMethod)) {
		    $feePlanList->addList(new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
			    return $feePlan->isPayNow();
		    }))));
	    }

        return $feePlanList;
    }
}
