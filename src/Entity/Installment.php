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

class Installment extends AbstractEntity
{
    protected int $customerFee;

    protected int $customerInterest;

    protected int $dueDate;

    protected int $purchaseAmount;

    protected string $state;

    /** Mapping of required fields */
    protected array $requiredFields = [
        'customerFee'      => 'customer_fee',
        'customerInterest' => 'customer_interest',
        'dueDate'          => 'due_date',
        'purchaseAmount'   => 'purchase_amount',
        'state'            => 'state',
    ];

    /** Mapping of optional fields */
    protected array $optionalFields = [
    ];

    /**
     * Returns the customer fee in cents
     * @return int
     */
    public function getCustomerFee(): int
    {
        return $this->customerFee;
    }

    /**
     * Returns the customer interest in cents
     * @return int
     */
    public function getCustomerInterest(): int
    {
        return $this->customerInterest;
    }

    /**
     * Returns the due date as a UNIX timestamp
     * @return int
     */
    public function getDueDate(): int
    {
        return $this->dueDate;
    }

    /**
     * Returns the purchase amount in cents
     * @return int
     */
    public function getPurchaseAmount(): int
    {
        return $this->purchaseAmount;
    }

    /**
     * Returns the state of the installment
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }
}
