<?php

namespace Alma\API\Domain\Entity;

use ArrayObject;

/**
 * Class PaymentPlan
 * Used to represent a list of Installments in payment objects.
 * @package Alma\API\Entity
 */
class PaymentPlan extends ArrayObject
{
    public function __construct($array = [], $flags = 0, $iteratorClass = "ArrayIterator")
    {
        parent::__construct($array, $flags, $iteratorClass);
    }

    /**
     * Adds an Installment to the list.
     *
     * @param Installment $installment
     * @return void
     */
    public function add(Installment $installment): void
    {
        $this[] = $installment;
    }

    /**
     * Returns the number of installments in the list.
     *
     * @return int
     */
    public function getInstallmentCount(): int
    {
        return $this->count();
    }

}
