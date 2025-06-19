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

namespace Alma\API\Endpoint;

use Alma\API\Entities\Order;
use Alma\API\Exceptions\Endpoint\OrderEndpointException;
use Alma\API\Exceptions\MissingKeyException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Lib\ArrayUtils;
use Alma\API\PaginatedResult;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class OrderEndpoint extends AbstractEndpoint
{
    const ORDERS_ENDPOINT_V1 = '/v1/orders';
    const ORDERS_ENDPOINT = '/v2/orders';
    private ArrayUtils $arrayUtils;

    public function __construct(ClientInterface $client)
    {
        parent::__construct($client);
        $this->arrayUtils = new ArrayUtils();
    }

    /**
     * @param string $orderId
     * @param array $orderData
     *
     * @return Order
     * @throws OrderEndpointException
     */
    public function update(string $orderId, array $orderData = []): Order
    {
        try {
            $request = null;
            $request = $this->createPostRequest(self::ORDERS_ENDPOINT_V1 . "/$orderId", $orderData);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new OrderEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new OrderEndpointException($response->getReasonPhrase(), $request, $response);
        }

        $json = $response->getJson();
        return new Order(end($json));
    }

    /**
     * @param string $orderId
     * @param string $carrier
     * @param string $trackingNumber
     * @param string|null $trackingUrl
     * @return void
     * @throws OrderEndpointException
     */
    public function addTracking(string $orderId, string $carrier, string $trackingNumber, ?string $trackingUrl = null)
    {
        $trackingData = [
            'carrier' => $carrier,
            'tracking_number' => $trackingNumber,
            'tracking_url' => $trackingUrl
        ];

        try {
            $request = null;
            $request = $this->createPostRequest(self::ORDERS_ENDPOINT . "/$orderId/shipment", $trackingData);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new OrderEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new OrderEndpointException($response->getReasonPhrase(), $request, $response);
        }
    }

    /**
     * @param int $limit
     * @param string|null $startingAfter
     * @param array $filters
     *
     * @return PaginatedResult
     * @throws OrderEndpointException
     */
    public function fetchAll(int $limit = 20, string $startingAfter = null, array $filters = array()): PaginatedResult
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

        try {
            $request = null;
            $request = $this->createGetRequest(self::ORDERS_ENDPOINT_V1, $args);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new OrderEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new OrderEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return new PaginatedResult(
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
     * @throws OrderEndpointException
     */
    public function fetch(string $orderId): Order
    {
        try {
            $request = null;
            $request = $this->createGetRequest(self::ORDERS_ENDPOINT_V1 . "/$orderId");
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new OrderEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new OrderEndpointException($response->getReasonPhrase(), $request, $response);
        }

        $json = $response->getJson();
        return new Order($json);
    }

    /**
     * @param array $orderData
     * @return void
     * @throws ParametersException
     */
    public function validateStatusData(array $orderData = array())
    {
        if (empty($orderData)) {
            throw new ParametersException('Missing in the required parameters (status, is_shipped) when calling orders.sendStatus');
        }

        try {
            $arrayUtils = new ArrayUtils();
            $arrayUtils->checkMandatoryKeys(['status', 'is_shipped'], $orderData);
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
