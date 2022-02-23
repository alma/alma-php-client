<?php

/**
 * Copyright (c) 2018 Alma / Nabla SAS
 *
 * THE MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and
 * to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @author    Alma / Nabla SAS <contact@getalma.eu>
 * @copyright Copyright (c) 2018 Alma / Nabla SAS
 * @license   https://opensource.org/licenses/MIT The MIT License
 *
 */

namespace Alma\API;

/**
 * Class ArrayUtils
 * @package Alma\API
 */
class ArrayUtils
{
    /**
     * @return array|null
     */
    public static function almaArrayMergeRecursive() {

        if ( func_num_args() < 2 ) {
            trigger_error( __METHOD__ . ' needs two or more array arguments', E_USER_WARNING );
            return null;
        }
        $arrays = func_get_args();
        $merged = array();
        while ( $arrays ) {
            $array = array_shift( $arrays );
            if ( $array === null ) {
                continue;
            }
            if ( ! is_array( $array ) ) {
                trigger_error( __METHOD__ . ' encountered a non array argument', E_USER_WARNING );
                return null;
            }
            if ( ! $array ) {
                continue;
            }
            foreach ( $array as $key => $value ) {
                if ( is_string( $key ) ) {
                    if ( is_array( $value ) && array_key_exists( $key, $merged ) && is_array( $merged[ $key ] ) ) {
                        $merged[ $key ] = call_user_func( __METHOD__, $merged[ $key ], $value );
                    } else {
                        $merged[ $key ] = $value;
                    }
                } else {
                    $merged[] = $value;
                }
            }
        }
        return $merged;
    }

    /**
     * @param $array
     * @return bool
     */
    public static function isAssocArray($array) {
        if (!is_array($array)) {
            return false;
        }
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}