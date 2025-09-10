<?php

namespace Alma\API\Domain\Helper;

interface EventHelperInterface
{
    public function addEvent(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void;

    public function addFilter(string $hook, callable $callback, int $priority = 10, int $acceptedArgs = 1): void;
}
