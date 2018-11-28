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

class ClientContext {
    public $api_key;
	public $logger;
    private $options;

    public function __construct($api_key, $logger, $options) {
        $this->api_key = $api_key;
		$this->logger = $logger;
		$this->options = $options;
	}

    /**
     * Returns the full API endpoint URL for the given path, depending on the current mode (live or test)
     *
     * @param $path
     * @return string
     */
	public function url_for($path) {
        $root = $this->options['api_root'][$this->options['mode']];
        return rtrim($root, '/') . '/' . ltrim($path, '/');
    }

    /**
     * @return int|false    Either not to force TLS (false), or the TLS subversion to force (i.e. x for TLS 1.x)
     */
    public function forced_tls_version() {
	    return $this->options['force_tls'];
    }
}
