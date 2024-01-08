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

use Alma\API\Exceptions\ParametersException;

class InsuranceValidator
{
    /**
     * @param int $productPrice
     * @throws ParametersException
     */
    public function checkPriceFormat($productPrice)
    {
        $validationProductReferenceIdRegex =  '/^[0-9]+$/';

        if(!preg_match($validationProductReferenceIdRegex, $productPrice)) {
            throw new ParametersException(sprintf(
                'The product price has a wrong format : "%s"',
                    $productPrice
            ));
        }
    }

    /**
     * @param string $param
     * @param string $name
     * @throws ParametersException
     */
    public function checkParamFormat($param, $name)
    {
        $validationProductReferenceIdRegex =  '/^[a-zA-Z0-9-_ ]+$/';

        if(
            !is_string($param)
            || !preg_match($validationProductReferenceIdRegex, $param)
        ) {
            throw new ParametersException(sprintf(
                'The "%s" field needs to be an integer has a wrong format : "%s"',
                $name,
                $param
            ));
        }
    }
}
