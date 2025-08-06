<?php

namespace Alma\API\Entity;

use ArrayObject;

class EligibilityList extends ArrayObject
{
    public function add(Eligibility $eligibility): void
    {
        $this[] = $eligibility;
    }

    public function getByPlanKey(string $planKey): Eligibility
    {
        $filter = array_values(array_filter($this->getArrayCopy(), function($eligibility) use ($planKey) {
            return $eligibility->getPlanKey() === $planKey;
        }));
        return $filter[0];
    }

    /**
     * Returns a list of Eligibility that are only available for the given payment method.
     * @param $paymentMethod
     * @return EligibilityList
     */
    public function filterEligibilityList($paymentMethod): EligibilityList
    {
        switch ($paymentMethod) {
            case 'credit':
                $eligibilityList = new EligibilityList(array_values(array_filter($this->getArrayCopy(), function(Eligibility $eligibility) {
                    return $eligibility->isCredit();
                })));
                break;
            case 'pnx':
                $eligibilityList = new EligibilityList(array_values(array_filter($this->getArrayCopy(), function(Eligibility $eligibility) {
                    return $eligibility->isPnXOnly();
                })));
                break;
            case 'pay-later':
                $eligibilityList = new EligibilityList(array_values(array_filter($this->getArrayCopy(), function(Eligibility $eligibility) {
                    return $eligibility->isPayLaterOnly();
                })));
                break;
            case 'pay-now':
                $eligibilityList = new EligibilityList(array_values(array_filter($this->getArrayCopy(), function(Eligibility $eligibility) {
                    return $eligibility->isPayNow();
                })));
                break;
            default:
                $eligibilityList = new EligibilityList();
        }

        return $eligibilityList;
    }
}
