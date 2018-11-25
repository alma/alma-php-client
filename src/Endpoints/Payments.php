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

namespace Alma\Endpoints;

use Alma\Client;
use Alma\Entities\Payment;
use Alma\RequestError;

class EligibilityResult {
	public $is_eligible;
	public $reasons;

	public function __construct($res) {
		$this->is_eligible = ($res->response_code == 200);

		if ($res->response_code === 406) {
			$this->reasons = $res->json["reasons"];
		}
	}
}

class Payments extends Base {
    const PAYMENTS_URL = Client::API_URL . '/v1/payments';

	/**
	 * @param array $order_data
	 *
	 * @return EligibilityResult
	 * @throws RequestError
	 */
	public function eligibility($order_data) {
		$res = $this->request(self::PAYMENTS_URL . '/eligibility')->set_request_body($order_data)->post();

		if ($res->response_code === 406) {
			$this->logger->info("Eligibility check failed for following reasons: " . print_r($res->json["reasons"], true));
		}

		return new EligibilityResult($res);
    }

	/**
	 * @param $data
	 *
	 * @return Payment
	 * @throws RequestError
	 */
	public function create_payment($data) {
		$res = $this->request(self::PAYMENTS_URL)->set_request_body($data)->post();

		if ($res->is_error()) {
			throw new RequestError($res->error_message, null, $res);
		}

		return new Payment($res->json);
    }

	/**
	 * @param $id string The external ID for the payment to fetch
	 *
	 * @return Payment
	 * @throws RequestError
	 */
	public function fetch($id) {
	    $res = $this->request(self::PAYMENTS_URL . "/$id")->get();

	    if ($res->is_error()) {
		    throw new RequestError($res->error_message, null, $res);
	    }

	    return new Payment($res->json);
    }
}
