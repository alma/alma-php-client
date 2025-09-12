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

namespace Alma\API\Domain\Entity;

class Order extends AbstractEntity
{
    /** @var string | null Merchant comment on the order. */
    protected ?string $comment;

    /** @var int Order creation date (on Alma's side) as a timestamp. */
    protected int $createdAt;

    /** @var string Order ID (on Alma's side). */
    protected string $externalId;

    /** @var string | null Merchant reference for this order. Enables to link an order and a payment made with Alma. */
    protected ?string $merchantReference;

    /** @var string Alma ID of the payment corresponding to this order. Not present since a payment was submitted. */
    protected string $paymentId;

    /** @var int|null Order update date (on Alma's side) as a timestamp. */
    protected ?int $updatedAt = null;

    /** Mapping of required fields */
    protected array $requiredFields = [
        'comment'           => 'comment',
        'createdAt'         => 'created',
        'externalId'        => 'id',
        'merchantReference' => 'merchant_reference',
        'paymentId'         => 'payment',
    ];

    /** Mapping of optional fields */
    protected array $optionalFields = [
        'updatedAt'         => 'updated',
    ];

    /**
     * Gets the merchant comment on the order.
     * @return string|null
     * @noinspection PhpUnused Used by implementations
     */
    public function getComment(): ?string
    {
        return $this->comment;
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
     * Gets the order ID (on Alma's side).
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getExternalId(): string
    {
        return $this->externalId;
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
     * Gets the Alma payment ID corresponding to this order.
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * Gets the order update date as a timestamp | can be null.
     * @return int|null
     */
    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }
}
