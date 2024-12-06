<?php

namespace Alma\API\Lib;

/**
 * Class BoolUtils
 * @package Alma\API
 */
class BoolUtils
{
    /**
     * Check if a var is a bool or null value
     *
     * @param $boolToCheck
     * @return bool
     */
    public static function isStrictlyBoolOrNull($boolToCheck)
    {
        return is_bool($boolToCheck) || $boolToCheck === null;
    }

}
