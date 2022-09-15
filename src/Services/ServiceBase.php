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
 */

namespace Alma\API\Services;

use Alma\API\ClientContext;
use Alma\API\Request;
use Alma\API\NotImplementedException;
use Alma\API\Services\PayloadInterface;

abstract class ServiceBase
{
    private static $instance = null;

    /**
     * @var ClientContext
     */
    protected $clientContext;

    /**
     * Base service constructor.
     *
     * @param $clientContext ClientContext
     */
    protected function __construct($clientContext)
    {
        $this->setClientContext($clientContext);
    }

    /**
     * @param  string $path
     * @return Request
     */
    protected function request($path)
    {
        return Request::build($this->clientContext, $this->clientContext->urlFor($path));
    }

    protected function getLogger()
    {
        return $this->clientContext->logger;
    }

    /**
     * @param  ClientContext $clientContext
     * @return Request
     */
    protected function setClientContext($clientContext)
    {
        $this->clientContext = $clientContext;
    }

    public static function setInstance($service)
    {
        self::$instance = $service;
    }

    public static function getInstance($clientContext)
    {
        if (self::$instance === null) {
            self::$instance = new static($clientContext);
        }

        return self::$instance;
    }

    public function getList(PayloadInterface $payload = null)
    {
        throw new NotImplementedException();
    }

    public function get($id, PayloadInterface $payload = null)
    {
        throw new NotImplementedException();
    }

    public function update($id, PayloadInterface $payload = null)
    {
        throw new NotImplementedException();
    }

    public function create(PayloadInterface $payload)
    {
        throw new NotImplementedException();
    }

    public function delete($id, PayloadInterface $payload = null)
    {
        throw new NotImplementedException();
    }
}
