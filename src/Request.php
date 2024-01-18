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

// In older versions of PHP (<= 5.5.19), those constants aren't defined – we do need them though
if (!defined('CURL_SSLVERSION_TLSv1_0')) {
    define('CURL_SSLVERSION_TLSv1_0', 4);
}

if (!defined('CURL_SSLVERSION_TLSv1_1')) {
    define('CURL_SSLVERSION_TLSv1_1', 5);
}

if (!defined('CURL_SSLVERSION_TLSv1_2')) {
    define('CURL_SSLVERSION_TLSv1_2', 6);
}

class Request
{
    private $context;
    private $url;
    private $curlHandle;
    private $queryParams = array();
    private $headers = array();
    private $hasData;

    /**
     * @param $context ClientContext    The current client context
     * @param $url     string           The URL to build a request for
     * @return Request
     */
    public static function build($context, $url)
    {
        return new self($context, $url);
    }

    /**
     * HTTP request constructor.
     *
     * @param $context ClientContext    The current client context
     * @param $url  string  The URL to build a request for
     */
    public function __construct($context, $url)
    {
        $this->context = $context;
        $this->url = $url;
        $this->hasData = false;
        $this->initCurl();
    }

    /**
     * @param string $customerSessionId
     * @return void
     */
    public function addCustomerSessionIdToHeader($customerSessionId)
	{
		$this->headers[] = 'X-Customer-Session-Id: ' . $customerSessionId;
	}

    /**
     * @param string $cartId
     * @return void
     */
    public function addCartIdToHeader($cartId)
	{
		$this->headers[] = 'X-Customer-Cart-Id: ' . $cartId;
	}

    private function initCurl()
    {
        $this->curlHandle = curl_init();

        $this->headers = array(
            'User-Agent: ' . $this->context->getUserAgentString(),
            'Authorization: Alma-Auth ' . $this->context->apiKey,
            'Accept: application/json',
        );

        // Never *print out* request results
        curl_setopt($this->curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curlHandle, CURLOPT_FAILONERROR, false);

        if ($forced_tls = $this->context->forcedTLSVersion()) {
            $tls_version = CURL_SSLVERSION_TLSv1_2;
            switch ($forced_tls) {
                case 0:
                    $tls_version = CURL_SSLVERSION_TLSv1_0;
                    break;
                case 1:
                    $tls_version = CURL_SSLVERSION_TLSv1_1;
            }

            curl_setopt($this->curlHandle, CURLOPT_SSLVERSION, $tls_version);
        }
    }

    private function buildURL()
    {
        $params = http_build_query($this->queryParams);
        $parsed_url = parse_url($this->url);

        $url = $parsed_url["scheme"] . '://';

        if (array_key_exists("user", $parsed_url)) {
            $url .= $parsed_url["user"];
            if ($parsed_url["pass"]) {
                $url .= ':' . $parsed_url["pass"];
            }
            $url .= '@';
        }

        $url .= $parsed_url["host"];

        if (array_key_exists("port", $parsed_url)) {
            $url .= ":" . $parsed_url["port"];
        }

        $url .= $parsed_url["path"];

        if (array_key_exists("query", $parsed_url)) {
            $params = $parsed_url["query"] . '&' . $params;
        }

        if ($params) {
            $url .= '?' . $params;
        }

        if (array_key_exists("fragment", $parsed_url)) {
            $url .= '#' . $parsed_url["fragment"];
        }

        return $url;
    }

    /**
     * @throws RequestError
     */
    private function exec()
    {
        $url = $this->buildURL();

        if (($port = parse_url($url, PHP_URL_PORT))) {
            curl_setopt($this->curlHandle, CURLOPT_PORT, $port);
        }
        curl_setopt($this->curlHandle, CURLOPT_URL, $url);
        curl_setopt($this->curlHandle, CURLOPT_HTTPHEADER, $this->headers);

        $curl_res = curl_exec($this->curlHandle);

        // Throw exception *only* if HTTP code is `0`, which seems to mean an actual failure
        if ($curl_res === false && curl_getinfo($this->curlHandle, CURLINFO_HTTP_CODE) == 0) {
            throw new RequestError(curl_error($this->curlHandle), $this);
        }

        $response = new Response($this->curlHandle, $curl_res);
        curl_close($this->curlHandle);
        return $response;
    }

    public function setRequestBody($data = array())
    {
        $body = $data ? json_encode($data) : '';

        curl_setopt($this->curlHandle, CURLOPT_POSTFIELDS, $body);

        if ($body) {
            $this->headers[] = 'Content-type: application/json';
            $this->hasData = true;
        }

        return $this;
    }

    public function setQueryParams($params = array())
    {
        if ($params == null) {
            $params = array();
        }

        $this->queryParams = $params;
        return $this;
    }

    /**
     * @return Response
     * @throws RequestError
     */
    public function get()
    {
        curl_setopt_array($this->curlHandle, array(
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POST => false,
            CURLOPT_HTTPGET => true,
        ));

        return $this->exec();
    }

    /**
     * @return Response
     * @throws RequestError
     */
    public function post()
    {
        // If no data was set, force an empty body to make sure we don't get a 411 error from some servers
        if (!$this->hasData) {
            $this->setRequestBody(null);
        }

        curl_setopt_array($this->curlHandle, array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPGET => false,
            CURLOPT_POST => true,
        ));

        return $this->exec();
    }

    /**
     * @return Response
     * @throws RequestError
     */
    public function put()
    {
        // If no data was set, force an empty body to make sure we don't get a 411 error from some servers
        if (!$this->hasData) {
            $this->setRequestBody(null);
        }

        curl_setopt_array($this->curlHandle, array(
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_HTTPGET => false,
            CURLOPT_POST => true,
        ));

        return $this->exec();
    }

    /**
     * @return Response
     * @throws RequestError
     */
    public function delete()
    {
        $this->setRequestBody(null);

        curl_setopt_array($this->curlHandle, array(
            CURLOPT_CUSTOMREQUEST => 'DELETE',
            CURLOPT_HTTPGET => false,
            CURLOPT_POST => false,
        ));

        return $this->exec();
    }
}
