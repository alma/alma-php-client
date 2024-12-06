<?php

namespace Alma\API\Lib;

class StringUtils
{
    /**
     * @param $stringToCheck
     * @return bool
     */
    public static function isAValidString($stringToCheck)
    {
        return is_string($stringToCheck) && trim($stringToCheck) !== '';
    }

}