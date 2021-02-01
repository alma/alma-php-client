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

class Response
{
    public $responseCode;
    public $json;
    public $errorMessage;

    public function __construct($curlHandle, $curlResult)
    {
        $this->responseCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);
        $this->json = json_decode($curlResult, true);

        if ($this->isError()) {
            if ($this->json && array_key_exists('message', $this->json)) {
                $this->errorMessage = $this->json['message'];
            } else {
                $this->errorMessage = curl_error($curlHandle);
            }
        }
    }

    public function isError()
    {
        return $this->responseCode >= 400 && $this->responseCode < 600;
    }
}

class EmptyResponse extends Response
{
    public function __construct()
    {
        $this->errorMessage = null;
        $this->json = [];
        $this->responseCode = null;
    }
}
