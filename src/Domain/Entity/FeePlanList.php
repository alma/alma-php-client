<?php

namespace Alma\API\Domain\Entity;

use Alma\API\Domain\Adapter\FeePlanInterface;
use Alma\API\Domain\Adapter\FeePlanListInterface;
use Alma\Gateway\Application\Entity\Form\FeePlanConfiguration;
use ArrayObject;
use OutOfBoundsException;

class FeePlanList extends ArrayObject implements FeePlanListInterface
{
    public function __construct($array = [], $flags = 0, $iteratorClass = "ArrayIterator")
    {
        parent::__construct(
            array_filter($array, function($item) { return $item instanceof FeePlanInterface; }),
            $flags,
            $iteratorClass
        );
    }

    public function add(FeePlanInterface $feePlan): void
    {
        $this[] = $feePlan;
    }

    public function addList(FeePlanListInterface $feePlanList): void
    {
        foreach ($feePlanList as $feePlan) {
            $this->add($feePlan);
        }
    }

    /**
     * Returns a FeePlan by its plan key.
     * @param string $planKey
     * @return FeePlanInterface
     * @throws OutOfBoundsException if the plan key does not exist in the list.
     */
    public function getByPlanKey(string $planKey): FeePlanInterface
    {
        $filter = array_values(array_filter($this->getArrayCopy(), function($feePlan) use ($planKey) {
            return $feePlan->getPlanKey() === $planKey;
        }));
        return $filter[0];
    }

    /**
     * Returns a list of Fee Plans that are only available for the given payment method.
     * @param array $paymentMethod
     * @return FeePlanListInterface
     */
    public function filterFeePlanList(array $paymentMethod): FeePlanListInterface
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
        if (in_array('paylater', $paymentMethod)) {
            $feePlanList->addList(new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
                return $feePlan->isPayLaterOnly();
            }))));
        }
        if (in_array('paynow', $paymentMethod)) {
            $feePlanList->addList(new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
                return $feePlan->isPayNow();
            }))));
        }

        return $feePlanList;
    }

    /**
     * Returns a FeePlanList containing only enabled FeePlans.
     *
     * @return FeePlanListInterface
     */
    public function filterEnabled(): FeePlanListInterface
    {
        $feePlanList = new FeePlanList();
        $feePlanList->addList(new FeePlanList(array_values(array_filter($this->getArrayCopy(), function(FeePlan $feePlan) {
            return $feePlan->isEnabled();
        }))));
        return $feePlanList;
    }
}
