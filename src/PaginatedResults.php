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

namespace Alma\API;

use Iterator;

class PaginatedResults implements Iterator
{
    protected $position = 0;
    protected $response;
    /**
     * @var callable
     */
    protected $nextPageCb;
    private $entities;

    /**
     * PaginatedResults constructor.
     *
     * @param Response $response
     * @param callable $nextPageCb
     */
    public function __construct($response, $nextPageCb)
    {
        $this->response = $response;
        $this->entities = isset($response->json['data']) ? $response->json['data'] : [];
        $this->nextPageCb = $nextPageCb;
    }

    public function rewind()
    {
        $this->position = 0;
    }

    public function current()
    {
        return $this->entities[$this->position];
    }

    public function key()
    {
        return $this->position;
    }

    public function next()
    {
        ++$this->position;
    }

    public function valid()
    {
        return isset($this->entities[$this->position]);
    }

    public function nextPage()
    {
        if (!array_key_exists('has_more', $this->response->json)) {
            return new self(new EmptyResponse(), null);
        }

        $callback = $this->nextPageCb;

        return $callback(array_slice($this->entities, -1, 1)[0]['id']);
    }
}