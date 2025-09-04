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
    /** @var string ID of the Payment owning this Order */
    protected string $paymentId;

    /**
     * @var string | null  Order reference from the merchant's platform
     */
    protected ?string $merchantReference;

    /** @var string | null  URL to the merchant's backoffice for that Order */
    protected ?string $merchantUrl;

    /** @var array  Free-form custom data */
    protected array $orderData;

    /**
     * @var string | null Order comment
     */
    protected ?string $comment;
    /**
     * @var int Order creation timestamp
     */
    protected int $createdAt;

    /**
     * @var string | null Customer URL
     */
    protected ?string $customerUrl;

    /**
     * @var string Order external ID
     */
    protected string $externalId;

    /**
     * @var int Order updated timestamp
     */
    protected int $updatedAt;

    protected array $requiredFields = [
    ];

    protected array $optionalFields = [
        'comment'           => 'comment',
        'createdAt'         => 'created',
        'customerUrl'       => 'customer_url',
        'orderData'         => 'data',
        'externalId'        => 'id',
        'merchantReference' => 'merchant_reference',
        'merchantUrl'       => 'merchant_url',
        'paymentId'         => 'payment',
        'updatedAt'         => 'updated',
    ];

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
    public function getUpdatedAt(): int
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
