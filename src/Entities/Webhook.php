<?php

/**
 * 2018-2019 Alma SAS
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
 * @author    Alma SAS <contact@getalma.eu>
 * @copyright 2018-2019 Alma SAS
 * @license   https://opensource.org/licenses/MIT The MIT License
 */


namespace Alma\API\Entities;

class Webhook extends Base
{
    const TYPE_INTEGRATION_CAPABILITIES = "integration_capabilities";

    /** @var string */
    public $type;
    /** @var string */
    public $url;

    /**
     * Verifies that the provided signature is valid for the given params and secret.
     *
     * @param string $signature The signature to validate
     * @param array $params An associative array of the webhook parameters provided in the HTTP call (all except `signature`)
     * @param string $secret The secret to use for signing
     * @param bool $urlEncode Whether params values should be url-encoded. Defaults to true, set to false if your values
     *             are already url-encoded
     *
     * @return bool Whether the signature is valid or not
     */
    public static function verifySignature($signature, $params, $secret, $urlEncode = true)
    {
        // Sort params by param name
        ksort($params, SORT_STRING);

        // Create string "param=value" pairs by URL encoding the value if required
        $data = [];
        foreach ($params as $param => $value) {
            if ($urlEncode) {
                $value = rawurlencode($value);
            }

            $data[] = "$param=$value";
        }

        $computed_signature = rtrim(strtr(base64_encode(hash_hmac('sha256', implode("&", $data), $secret, true)), '+/', '-_'), '=');

        if (is_callable('hash_equals')) {
            return hash_equals($signature, $computed_signature);
        } else {
            return $signature === $computed_signature;
        }
    }
}
