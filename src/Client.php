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

use Alma\Endpoints;

class ParamsError extends \Exception {};
class DependenciesError extends \Exception {};

class Client {
    const API_URL = 'https://api.getalma.eu';

    protected $context;

    /***** API ENDPOINTS *****/
	/**
	 * @var Endpoints\Payments
	 */
	public $payments;

	/**
	 * @var Endpoints\Merchants
	 */
	public $merchants;
	/*************************/

	/**
	 * Alma client initialization.
	 *
	 * @param string $api_key a valid API key for the service
	 *
	 * @param $logger
	 *
	 * @throws DependenciesError
	 * @throws ParamsError
	 */
    public function __construct($api_key, $logger) {
        $this->check_dependencies();

        if (empty($api_key)) {
            throw new ParamsError('An API key is required to instantiate new Alma\Client');
        }

        $this->context = new ClientContext($api_key, $logger);
        $this->init_endpoints();
    }

    /**
     * @throws DependenciesError
     */
    private function check_dependencies() {
        if (!function_exists('curl_init')) {
            throw new DependenciesError('Alma requires the CURL PHP extension.');
        }

        if (!function_exists('json_decode')) {
            throw new DependenciesError('Alma requires the JSON PHP extension.');
        }

        $openssl_exception = new DependenciesError('Alma requires OpenSSL >= 1.0.1');
        if (!defined( 'OPENSSL_VERSION_TEXT')) {
            throw $openssl_exception;
        }

        preg_match('/^(?:Libre|Open)SSL ([\d.]+)/', OPENSSL_VERSION_TEXT, $matches);
        if (empty($matches[1])) {
            throw $openssl_exception;
        }

        if (!version_compare($matches[1], '1.0.1', '>=')) {
            throw $openssl_exception;
        }
    }

    private function init_endpoints() {
    	$this->payments = new Endpoints\Payments($this->context);
    	$this->merchants = new Endpoints\Merchants($this->context);
    }
}
