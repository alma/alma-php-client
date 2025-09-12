<?php

namespace Alma\API\Domain\Entity;

use Alma\API\Entity\Eligibility;
use ArrayObject;

class EligibilityList extends ArrayObject
{
    /**
     * Adds an Eligibility to the list.
     * @param Eligibility $eligibility
     * @return void
     */
    public function add(Eligibility $eligibility): void
    {
        $this[] = $eligibility;
    }

    /**
     * Returns the Eligibility for the given plan key.
     *
     * @param string $planKey
     * @return Eligibility
     */
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
