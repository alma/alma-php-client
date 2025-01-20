<?php

namespace Alma\API\Entities\DTO\MerchantBusinessEvent;

abstract class AbstractBusinessEvent
{
    /**
     * @var string
     */
    protected $eventType;

    /**
     * Get Event Type for merchant business event
     *
     * @return string
     */
    public function getEventType()
    {
        return $this->eventType;
    }

}