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

class Refund extends AbstractEntity
{
    /** @var int The amount of the refund */
    protected int $amount;

    /** @var int The date of the refund */
    protected int $createdAt;

    /** @var string The ID of the refund */
    protected string $id;

    /** @var string The MerchantReference of the refund */
    protected string $merchantReference;

    /** Mapping of required fields */
    protected array $requiredFields = [
        'id'                 => 'id',
        'createdAt'          => 'created',
        'amount'             => 'amount',
    ];

    /** Mapping of optional fields */
    protected array $optionalFields = [
        'merchantReference' => 'merchant_reference',
    ];

    /**
     * Get the refund ID
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get the creation timestamp
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Get the amount in cents
     * @return int
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * Set the amount in cents
     * @param int $amount
     * @return $this
     */
    public function setAmount(int $amount): self {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get the merchant reference
     * @return string
     */
    public function getMerchantReference(): string
    {
        return $this->merchantReference;
    }

    /**
     * Set the merchant reference
     * @param string $merchantReference
     * @return $this
     */
    public function setMerchantReference(string $merchantReference): self {
        $this->merchantReference = $merchantReference;
        return $this;
    }
}
