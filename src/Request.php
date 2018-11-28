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

class RequestError extends \Exception {
	/**
	 * @var Request|null
	 */
	public $request;
	/**
	 * @var Response|null
	 */
	public $response;

	public function __construct( $message = "", $request = null, $response = null ) {
		parent::__construct( $message);

		$this->request = $request;
		$this->response = $response;
	}
};

class Request {
    private $context;
    private $url;
    private $curl_handle;
    private $query_params = array();
    private $headers = array();

    /**
     * @param $context ClientContext    The current client context
     * @param $url     string           The URL to build a request for
     * @return Request
     */
    static public function build($context, $url) {
        return new self($context, $url);
    }

    /**
     * HTTP request constructor.
     *
     * @param $context ClientContext    The current client context
     * @param $url  string  The URL to build a request for
     */
    public function __construct($context, $url) {
        $this->context = $context;
        $this->url = $url;
        $this->init_curl();
    }

    private function init_curl() {
        $this->curl_handle = curl_init();

        $this->headers = array(
	        'Authorization: Alma-Auth ' . $this->context->api_key,
	        'Accept: application/json',
        );

        // Never *print out* request results
        curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl_handle, CURLOPT_FAILONERROR, true);

        if ($forced_tls = $this->context->forced_tls_version()) {
            $tls_version = CURL_SSLVERSION_TLSv1_2;
            switch ($forced_tls) {
                case 0:
                    $tls_version = CURL_SSLVERSION_TLSv1_0;
                    break;
                case 1:
                    $tls_version = CURL_SSLVERSION_TLSv1_1;
            }

	        curl_setopt ($this->curl_handle, CURLOPT_SSLVERSION, $tls_version);
        }
    }

    private function build_url() {
        $params = http_build_query($this->query_params);
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

        if($params) {
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
    private function exec() {
        $url = $this->build_url();

        if (($port = parse_url($url, PHP_URL_PORT))) {
            curl_setopt($this->curl_handle, CURLOPT_PORT, $port);
        }
        curl_setopt($this->curl_handle, CURLOPT_URL, $url);
	    curl_setopt($this->curl_handle, CURLOPT_HTTPHEADER, $this->headers);

        $curl_res = curl_exec($this->curl_handle);

	    // Throw exception *only* if HTTP code is `0`, which seems to mean an actual failure
        if ($curl_res === false && curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE) == 0) {
            throw new RequestError(curl_error($this->curl_handle), $this);
        }

        $response = new Response($this->curl_handle, $curl_res);
        curl_close($this->curl_handle);
        return $response;
    }

    public function set_request_body($data = array()) {
        $body = $data ? json_encode($data) : '';
        curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $body);
	    $this->headers[] = 'Content-type: application/json';
        return $this;
    }

    public function set_query_params($params = array()) {
        if ($params == null) {
            $params = array();
        }

        $this->query_params = $params;
        return $this;
    }

	/**
	 * @return Response
	 * @throws RequestError
	 */
	public function get() {
        curl_setopt_array($this->curl_handle, array(
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
	public function post() {
        curl_setopt_array($this->curl_handle, array(
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPGET => false,
            CURLOPT_POST => true,
        ));

        return $this->exec();
    }
}
