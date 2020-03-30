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

namespace Alma\API\Endpoints;

use Alma\API\Entities\Order;
use Alma\API\RequestError;
use Alma\API\PaginatedResults;

class Orders extends Base
{
    const ORDERS_PATH = '/v1/orders';

    /**
     * @param string $orderId
     * @param array $orderData
     *
     * @return Order
     * @throws RequestError
     */
    public function update($orderId, $orderData)
    {
        $response = $this->request(self::ORDERS_PATH . "/{$orderId}")->setRequestBody($orderData)->post();
        return new Order($response);
    }

    /**
     * @param string $orderId
     * @param array $orderData
     *
     * @return PaginatedResults
     * @throws RequestError
     */
    public function fetchAll($limit = 20, $startingAfter = null, $filters = array())
    {
        $args = array(
            'limit' => $limit,
        );

        if ($startingAfter) {
            $args['starting_after'] = $startingAfter;
        }

        if ($filters) {
            foreach ($filters as $key => $filter) {
                $args[$key] = $filters;
            }
        }

        $response = $this->request(self::ORDERS_PATH)->setQueryParams($args)->get();
        return new PaginatedResults(
            $response,
            self::class,
            function($startingAfter) use ($limit, $filters) {
                $this->fetchAll($limit, $startingAfter, $filters);
            }
        );
    }

    /**
     * @param string $orderId
     *
     * @return Order
     * @throws RequestError
     */
    public function fetch($orderId)
    {
        $response = $this->request(self::ORDERS_PATH . "/{$orderId}")->get();
        return new Order($response);
    }
}
