<?php

namespace Alma\API\Lib;

/**
 * Class StringUtils
 * @package Alma\API
 */
class StringUtils
{
    /**
     * Check if it's a non-empty string
     *
     * @param $stringToCheck
     * @return bool
     */
    public static function isAValidString($stringToCheck): bool
    {
        return is_string($stringToCheck) && trim($stringToCheck) !== '';
    }
}
