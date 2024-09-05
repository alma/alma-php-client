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

use Alma\API\ParamsError;

/**
 * Class PaymentValidator
 * @package Alma\API
 */
class PaymentValidator
{

    const HEADER_SIGNATURE_KEY = 'X-Alma-Signature';

    /**
     * Ensure that the purchase amount is an integer
     *
     * @param $data
     * @return void
     * @throws ParamsError
     */
    public static function checkPurchaseAmount($data)
    {
        if (
            !empty($data['payment']['purchase_amount'])
            && !is_int($data['payment']['purchase_amount'])
        ) {
            throw new ParamsError(sprintf(
                'The "purchase_amount" field needs to be an integer. "%s" found ',
                gettype($data['payment']['purchase_amount'])
            ));
        }
    }

    /**
     * @param string $data
     * @param string $apiKey
     * @param string $signature
     * @return bool
     */
    public function isHmacValidated($data, $apiKey,  $signature)
    {
        return is_string($data) &&
            is_string($apiKey) &&
            hash_hmac('sha256', $data, $apiKey) === $signature;
    }
}
