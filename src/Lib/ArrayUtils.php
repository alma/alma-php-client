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

namespace Alma\API\Lib;

use Alma\API\Exceptions\AlmaException;
use Alma\API\Exceptions\MissingKeyException;
use Alma\API\Exceptions\ParametersException;

/**
 * Class ArrayUtils
 * @package Alma\API
 */
class ArrayUtils
{
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

    /**
     * @param array $keys
     * @param array $array
     * @return void
     * @throws MissingKeyException
     */
    public function checkMandatoryKeys($keys, $array)
    {
        foreach ($keys as $key) {
            if(!array_key_exists($key, $array)){
                throw new MissingKeyException(
                    sprintf(
                        'The key "%s" is missing from the array "%s"',
                        $key,
                        json_encode($array)
                ));
            }
        }
    }

    /**
     * Return the slug of a string to be used in a URL.
     *
     * @param string $textToSlugify
     *
     * @throws ParametersException
     *
     * @return String
     */
    public function slugify($textToSlugify){
        if(!is_string($textToSlugify)) {
            throw new ParametersException('Status must be a string');
        }

        // trim
        $text = trim($textToSlugify, '-');

        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '_', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);


        // remove duplicated - symbols
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (
            empty($text)
            || '_' === $text
        ) {
            throw new ParametersException(sprintf('The slug is empty, status "%s"', $textToSlugify));
        }

        return $text;
    }

}
