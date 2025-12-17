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

namespace Alma\API\Infrastructure;

use Iterator;

abstract class AbstractPaginatedResult
{
    /** @var int */
    protected int $position = 0;

    /** @var ResponseInterface */
    protected ResponseInterface $response;

    /**
     * @var callable
     */
    protected $nextPageCallback;
    protected $entities;

    /**
     * PaginatedResults constructor.
     *
     * @param Response $response
     * @param callable|null $nextPageCallback
     */
    public function __construct(ResponseInterface $response, ?callable $nextPageCallback)
    {
        $this->response         = $response;
        $this->entities         = $response->getJson()['data'] ?? [];
        $this->nextPageCallback = $nextPageCallback;
    }

    /**
     * Rewind the iterator.
     * @return void
     */
    public function rewind(): void
    {
        $this->position = 0;
    }

    /**
     * Return the current entity key.
     * @return int The current entity key.
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Move to the next entity.
     * @return void
     */
    public function next(): void
    {
        ++$this->position;
    }

    /**
     * Check if there is a next entity.
     * @return bool
     */
    public function valid(): bool
    {
        return isset($this->entities[$this->position]);
    }
}
