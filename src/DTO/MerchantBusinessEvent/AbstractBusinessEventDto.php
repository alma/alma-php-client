<?php

namespace Alma\API\DTO\MerchantBusinessEvent;

abstract class AbstractBusinessEventDto
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
    public function getEventType(): string
    {
        return $this->eventType;
    }

}
