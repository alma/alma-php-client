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

use Alma\API\Entities\DTO\CustomerDto;
use Alma\API\Entities\DTO\OrderDto;
use Alma\API\Entities\DTO\PaymentDto;
use Alma\API\Entities\Order;
use Alma\API\Entities\Payment;
use Alma\API\Exceptions\Endpoint\PaymentEndpointException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\Payloads\Refund;
use Psr\Http\Client\ClientExceptionInterface;

class PaymentEndpoint extends AbstractEndpoint
{
    const PAYMENTS_ENDPOINT = '/v1/payments';

    /**
     * @param PaymentDto $payment_dto
     * @param OrderDto $order_dto
     * @param CustomerDto $customer_dto
     * @return Payment
     * @throws PaymentEndpointException
     */
    public function create(
        PaymentDto $payment_dto,
        OrderDto $order_dto,
        CustomerDto $customer_dto
    ): Payment
    {
        $data = array(
            'payment' => $payment_dto->toArray(),
            'order' => $order_dto->toArray(),
            'customer' => $customer_dto->toArray(),
        );
        $request = null;
        try {
            $request = $this->createPostRequest(self::PAYMENTS_ENDPOINT, $data);
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new PaymentEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new PaymentEndpointException(var_export($data, true), $request, $response);
        }

        return new Payment($response->getJson());
    }

    /**
     * @param string $id The ID of the payment to cancel
     *
     * @return true
     * @throws PaymentEndpointException
     */
    public function cancel(string $id): bool
    {
        $request = null;
        try {
            $request = $this->createPutRequest(self::PAYMENTS_ENDPOINT . sprintf('/%s/cancel', $id));

            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new PaymentEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            $this->logger->error(sprintf('An error occurred while canceling the payment %s', $id), [$response->getReasonPhrase()]);
            throw new PaymentEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return true;
    }

    /**
     * @param string $id The external ID for the payment to fetch
     *
     * @return Payment
     * @throws PaymentEndpointException
     */
    public function fetch(string $id): Payment
    {
        $request = null;
        try {
            $request = $this->createGetRequest(self::PAYMENTS_ENDPOINT . "/$id");
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new PaymentEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new PaymentEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return new Payment($response->getJson());
    }

    /**
     * @param string $id
     * @param array $data
     *
     * @return Payment
     * @throws PaymentEndpointException
     */
    public function edit(string $id, array $data = []): Payment
    {
        $request = null;
        try {
            $request = $this->createPostRequest(self::PAYMENTS_ENDPOINT . "/$id", $data);
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new PaymentEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new PaymentEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return new Payment($response->getJson());
    }

    /**
     * @param string $id The ID of the payment to flag as potential fraud
     * @param string|null $reason An optional message indicating why this payment is being flagged
     *
     * @return bool
     * @throws PaymentEndpointException
     */
    public function flagAsPotentialFraud(string $id, string $reason = null): bool
    {
        $request = null;
        $data = [];
        if (!empty($reason)) {
            $data = array("reason" => $reason);
        }

        try {
            $request = $this->createPostRequest(self::PAYMENTS_ENDPOINT . "/$id/potential-fraud", $data);
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new PaymentEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new PaymentEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return true;
    }

    /**
     * Refund a payment partially
     * @param string $id ID of the payment to be refunded
     * @param int $amount Amount that should be refunded. Must be expressed as a cents
     *                          integer
     * @param string $merchantReference Merchant reference for the refund to be executed
     * @param string $comment
     *
     * @return Payment
     * @throws ParametersException
     * @throws PaymentEndpointException
     */
    public function partialRefund(string $id, int $amount, string $merchantReference = "", string $comment = ""): Payment
    {
        return $this->doRefund(
            Refund::create($id, $amount, $merchantReference, $comment)
        );
    }

    /**
     * Totally refund a payment
     * @param string $id ID of the payment to be refunded
     * @param string $merchantReference Merchant reference for the refund to be executed
     * @param string $comment
     *
     * @return Payment
     * @throws ParametersException
     * @throws PaymentEndpointException
     */
    public function fullRefund(string $id, string $merchantReference = "", string $comment = ""): Payment
    {
        return $this->doRefund(
            Refund::create($id, null, $merchantReference, $comment)
        );
    }

    /**
     * Totally refund a payment
     * @param Refund $refundPayload contains all the refund info
     *
     * @return Payment
     * @throws PaymentEndpointException
     */
    private function doRefund(Refund $refundPayload): Payment
    {
        $id = $refundPayload->getId();
        try {
            $request = null;
            $request = $this->createPostRequest(self::PAYMENTS_ENDPOINT . "/$id/refund", $refundPayload->getRequestBody());
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new PaymentEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new PaymentEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return new Payment($response->getJson());
    }

    /**
     * @param string $id ID of the payment to be triggered
     *
     * @return Payment
     * @throws PaymentEndpointException
     */
    public function trigger(string $id): Payment
    {
        try {
            $request = null;
            $request = $this->createPostRequest(self::PAYMENTS_ENDPOINT . "/$id/trigger");
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new PaymentEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new PaymentEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return new Payment($response->getJson());
    }

    /**
     * Adds an Order to the given Payment, possibly overwriting existing orders
     *
     * @param string $id ID of the payment to which the order must be added
     * @param array $orderData Data of the Order
     * @param bool $overwrite Should the order replace any other order set on the payment, or be appended to the payment's orders (default: false)
     *
     * @return Order
     *
     * @throws PaymentEndpointException
     */
    public function addOrder(string $id, array $orderData = [], bool $overwrite = false): Order
    {
        if ($overwrite) {
            return $this->overwriteOrder($id, $orderData);
        } else {
            try {
                $request = null;
                $request = $this->createPutRequest(self::PAYMENTS_ENDPOINT . "/$id/orders", array("order" => $orderData));
                $response = $this->client->sendRequest($request);
            } catch (RequestException|ClientExceptionInterface $e) {
                throw new PaymentEndpointException($e->getMessage(), $request);
            }
        }

        if ($response->isError()) {
            throw new PaymentEndpointException($response->getReasonPhrase(), $request, $response);
        }

        $json = $response->getJson();

        return new Order(end($json));
    }

    /**
     * Overwrite an Order to the given Payment, possibly overwriting existing orders
     *
     * @param string $id ID of the payment to which the order must be added
     * @param array $orderData Data of the Order
     *
     * @return Order
     *
     * @throws PaymentEndpointException
     */
    public function overwriteOrder(string $id, array $orderData = []): Order
    {
        try {
            $request = null;
            $request = $this->createPostRequest(self::PAYMENTS_ENDPOINT . "/$id/orders", array("order" => $orderData));
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new PaymentEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new PaymentEndpointException($response->getReasonPhrase(), $request, $response);
        }

        $json = $response->getJson();

        return new Order(end($json));
    }

    /**
     * Add order status to Alma Order by merchant_order_reference
     *
     * @param string $paymentId
     * @param string $merchantOrderReference
     * @param string $status
     * @param bool | null $isShipped
     * @return true
     * @throws PaymentEndpointException
     */
    public function addOrderStatusByMerchantOrderReference(
        string $paymentId,
        string $merchantOrderReference,
        string $status,
        ?bool  $isShipped = null
    ): bool
    {
        try {
            $request = null;
            $request = $this->createPostRequest(
                self::PAYMENTS_ENDPOINT . sprintf('/%s/orders/%s/status', $paymentId, $merchantOrderReference),
                ['status' => $status, 'is_shipped' => $isShipped]
            );
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new PaymentEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new PaymentEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return true;
    }

    /**
     * Sends an SMS to the customer, containing a link to the payment's page
     * /!\ Your account must be authorized by Alma to use that endpoint; it will otherwise fail with a 403 error
     *
     * @param string $id ID of the payment to send an SMS for
     *
     * @return bool
     *
     * @throws PaymentEndpointException
     */
    public function sendSms(string $id): bool
    {
        try {
            $request = null;
            $request = $this->createPostRequest(self::PAYMENTS_ENDPOINT . "/$id/send-sms");
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new PaymentEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new PaymentEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return true;
    }
}
