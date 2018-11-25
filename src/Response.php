<?php

/*
 * Copyright (c) 2018 Alma
 * http://www.getalma.eu/
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 *
 */

namespace Alma;

class Response {
    public $response_code;
    public $json;
    public $error_message;

    public function __construct($curl_handle, $curl_result) {
        $this->response_code = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		$this->json = json_decode($curl_result, true);

        if ($this->is_error()) {
	        if ($this->json && array_key_exists('message', $this->json)) {
	        	$this->error_message = $this->json['message'];
	        } else {
	        	$this->error_message = curl_error($curl_handle);
	        }
        }
    }

    public function is_error() {
    	return $this->response_code >= 400 && $this->response_code < 600;
    }
}
