<?php

namespace Alma\API\Lib;

class BoolUtils
{
    /**
     * @param $boolToCheck
     * @return bool
     */
    public static function isStrictlyBoolOrNull($boolToCheck)
    {
        return is_bool($boolToCheck) || $boolToCheck === null;
    }

}