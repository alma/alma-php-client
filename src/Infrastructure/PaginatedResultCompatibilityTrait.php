<?php

namespace Alma\API\Infrastructure;

// Patch to be compatible with PHP7 and PHP8
use Alma\API\Infrastructure\Endpoint\PaginatedResult;

if (PHP_VERSION_ID < 80000) {
    class_alias(
        PaginatedResult7::class,
        PaginatedResult::class
    );
} else {
    class_alias(
        PaginatedResult8::class,
        PaginatedResult::class
    );
}

/**
 * The ony interrest of this trait is to add aliases to PaginatedResult for compatibility reasons.
 */
trait PaginatedResultCompatibilityTrait
{

}
