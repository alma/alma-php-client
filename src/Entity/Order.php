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

namespace Alma\API\Entity;

class Order extends AbstractEntity
{
    /** @var string Order ID (on Alma's side). */
    protected string $externalId;

    /** @var int Order creation date (on Alma's side) as a timestamp. */
    protected int $createdAt;

    /** @var string | null Merchant reference for this order. Enables to link an order and a payment made with Alma. */
    protected ?string $merchantReference;

    /** @var string | null Merchant backoffice URL for this order. */
    protected ?string $merchantUrl;

    /** @var string | null Customer order tracking URL for this order. */
    protected ?string $customerUrl;

    /** @var string Alma ID of the payment corresponding to this order. Not present since a payment was submitted. */
    protected string $paymentId;

    /** @var array Array containing arbitrary data entered by the merchant */
    protected array $orderData;

    /** @var string | null Merchant comment on the order. */
    protected ?string $comment;

    /** Mapping of required fields */
    protected array $requiredFields = [
        'externalId'        => 'id',
        'createdAt'         => 'created',
        'paymentId'         => 'payment',
        'orderData'         => 'data',
    ];

    /** Mapping of optional fields */
    protected array $optionalFields = [
        'merchantReference' => 'merchant_reference',
        'merchantUrl'       => 'merchant_url',
        'customerUrl'       => 'customer_url',
        'comment'           => 'comment',
    ];

    /**
     * Gets the order ID (on Alma's side).
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * Gets the order creation date as a timestamp.
     * @return int
     * @noinspection PhpUnused Used by implementations
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Gets the merchant reference for this order.
     * @return string|null
     * @noinspection PhpUnused Used by implementations
     */
    public function getMerchantReference(): ?string
    {
        return $this->merchantReference;
    }

    /**
     * Gets the merchant backoffice URL for this order.
     * @return string|null
     * @noinspection PhpUnused Used by implementations
     */
    public function getMerchantUrl(): ?string
    {
        return $this->merchantUrl;
    }

    /**
     * Gets the customer order tracking URL for this order.
     * @return string|null
     * @noinspection PhpUnused Used by implementations
     */
    public function getCustomerUrl(): ?string
    {
        return $this->customerUrl;
    }

    /**
     * Gets the Alma payment ID corresponding to this order.
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * Gets the arbitrary data entered by the merchant.
     * @return array
     * @noinspection PhpUnused Used by implementations
     */
    public function getOrderData(): array
    {
        return $this->orderData;
    }

    /**
     * Gets the merchant comment on the order.
     * @return string|null
     * @noinspection PhpUnused Used by implementations
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }
}
