<?php

namespace Alma\API\Domain\Helper;

interface EventHelperInterface
{
    /**
     * Add an event to the event listener
     *
     * @param string $event The event name
     * @param callable $callback The callback function
     * @param int $priority The priority of the callback
     * @param int $acceptedArgs The number of arguments the callback accepts
     *
     * @return void
     */
    public static function addEvent(string $event, callable $callback, int $priority = 10, int $acceptedArgs = 1): void;
}
