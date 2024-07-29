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
use Alma\API\Exceptions\AlmaException;
use Alma\API\Exceptions\MissingKeyException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Lib\ArrayUtils;
use Alma\API\RequestError;
use Alma\API\PaginatedResults;

class Orders extends Base
{
    /**
     * @var ArrayUtils
     */
    public $arrayUtils;

    const ORDERS_PATH = '/v1/orders';
    const ORDERS_PATH_V2 = '/v2/orders';

    public function __construct($client_context)
    {
        parent::__construct($client_context);

        $this->arrayUtils = new ArrayUtils();
    }

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
        return new Order($response->json);
    }

    /**
     * @param string $orderId
     * @param string $carrier
     * @param string $trackingNumber
     * @param string|null $trackingUrl
     * @return void
     * @throws AlmaException
     */
    public function addTracking($orderId, $carrier, $trackingNumber, $trackingUrl = null)
    {
        $trackingData = [
            'carrier' => $carrier,
            'tracking_number' => $trackingNumber,
            'tracking_url' => $trackingUrl
        ];
        $response = $this->request(self::ORDERS_PATH_V2 . "/{$orderId}/shipment")
            ->setRequestBody($trackingData)
            ->post();
        if ($response->isError()) {
            throw new RequestException($response->errorMessage, null, $response);
        }
    }

    /**
     * @param int $limit
     * @param string|null $startingAfter
     * @param array $filters
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
            function ($startingAfter) use ($limit, $filters) {
                return $this->fetchAll($limit, $startingAfter, $filters);
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
        return new Order($response->json);
    }

    /**
     * @param string $orderExternalId
     * @param array $orderData
     * @return void
     * @throws ParametersException
     * @throws RequestException
     */
    public function sendStatus($orderExternalId, $orderData = array())
    {
        $this->validateStatusData($orderData);
        $label = $this->arrayUtils->slugify($orderData['status']);

        try {
            $response = $this->request(self::ORDERS_PATH_V2 . "/{$orderExternalId}/status")->setRequestBody(array(
                'status' => $label,
                'is_shipped' => $orderData['is_shipped'],
            ))->post();
        } catch (AlmaException $e) {
            $this->logger->error('Error sending status');
            throw new RequestException('Error sending status', $e);
        }

        if ($response->isError()) {
            throw new RequestException($response->errorMessage, null, $response);
        }
    }

    /**
     * @param array $orderData
     * @return void
     * @throws ParametersException
     */
    public function validateStatusData($orderData = array())
    {
        if (count($orderData) == 0) {
            throw new ParametersException('Missing in the required parameters (status, is_shipped) when calling orders.sendStatus');
        }

        try {
            $this->arrayUtils->checkMandatoryKeys(['status', 'is_shipped'], $orderData);
        } catch (MissingKeyException $e) {
            throw new ParametersException('Error in the required parameters (status, is_shipped) when calling orders.sendStatus', 0, $e);
        }

        if (!is_bool($orderData['is_shipped'])) {
            throw new ParametersException('Parameter "is_shipped" must be a boolean');
        }

        if (!$orderData['status']) {
            throw new ParametersException('Missing the required parameter "status" when calling orders.sendStatus');
        }
    }
}
