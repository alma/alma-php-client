<?php

namespace Alma\API\Domain\Entity;

use ArrayObject;

class OrderList extends ArrayObject
{
    public function __construct($array = [], $flags = 0, $iteratorClass = "ArrayIterator")
    {
        parent::__construct($array, $flags, $iteratorClass);
    }

    public function add(Order $order): void
    {
        $this[] = $order;
    }

}