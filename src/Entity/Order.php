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

class Order
{
    /** @var string ID of the Payment owning this Order
     * @deprecated
     */
    public $payment;

    /** @var string ID of the Payment owning this Order */
    private $paymentId;

    /**
     * @var string | null  Order reference from the merchant's platform
     */
    private $merchantReference;

    /** @var string | null  URL to the merchant's backoffice for that Order */
    private $merchantUrl;

    /**
     * @var array  Free-form custom data
     * @deprecated
     * */
    public $data;

    /** @var array  Free-form custom data */
    private $orderData;

    /**
     * @var string | null Order comment
     */
    private $comment;
    /**
     * @var int Order creation timestamp
     */
    private $createdAt;

    /**
     * @var string | null Customer URL
     */
    private $customerUrl;

    /**
     * @var string Order external ID
     */
    private $externalId;

    /**
     * @var string Order updated timestamp
     */
    private $updatedAt;

    public function __construct($orderDataArray)
    {
        $this->comment = $orderDataArray['comment'];
        $this->createdAt = $orderDataArray['created'];
        $this->customerUrl = $orderDataArray['customer_url'];
        $this->orderData = $orderDataArray['data'];
        $this->externalId = $orderDataArray['id'];
        $this->merchantReference = $orderDataArray['merchant_reference'];
        $this->merchantUrl = $orderDataArray['merchant_url'];
        $this->paymentId = $orderDataArray['payment'];
        $this->updatedAt = $orderDataArray['updated'] ?? null;
    }

    /**
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * @return string|null
     */
    public function getMerchantReference(): ?string
    {
        return $this->merchantReference;
    }

    /**
     * @return string|null
     */
    public function getMerchantUrl(): ?string
    {
        return $this->merchantUrl;
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }

    /**
     * @return string|null
     */
    public function getComment(): ?string
    {
        return $this->comment;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @return string|null
     */
    public function getCustomerUrl(): ?string
    {
        return $this->customerUrl;
    }

    /**
     * @return int
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return array
     */
    public function getOrderData(): array
    {
        return $this->orderData;
    }
}
