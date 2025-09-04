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

use Alma\API\Exception\ParametersException;

class Payment extends AbstractEntity
{
    /** @var string Payment is ongoing */
    const STATE_IN_PROGRESS = 'in_progress';

    /** @var string Payment has been fully paid, either at once after being scored negatively, or after all installments
     *              have been paid for. Note that by extension, a payment that has no amount due left after partial or
     *              total refunds will be considered PAID as well.
     */
    const STATE_PAID = 'paid';

    const FRAUD_AMOUNT_MISMATCH = 'amount_mismatch';
    const FRAUD_STATE_ERROR = 'state_error';

    /**
     * @var Installment[] Array of installments, representing the payment plan for this payment.
     */
    protected array $paymentPlan;

    /** @var Order[] List of orders associated to that payment */
    protected array $orders;

    /** @var Refund[] List of refunds for that payment */
    protected array $refunds;

    protected array $requiredFields = [
        'paymentPlan' => 'payment_plan',
        'orders'      => 'orders',
        'refunds'     => 'refunds',
    ];

    protected array $optionalFields = [
    ];

    /**
     * @param array $paymentData
     * @throws ParametersException
     */
    public function __construct(array $paymentData)
    {
        $this->paymentPlan = array();
        $this->orders = array();
        $this->refunds = array();
        $values = $this->prepareValues($paymentData, false);

        foreach ($values['payment_plan'] as $installment) {
            $this->paymentPlan[] = new Installment($installment);
        }

        foreach ($values['orders'] as $order) {
            $this->orders[] = new Order($order);
        }

        foreach ($values['refunds'] as $refund) {
            $this->refunds[] = new Refund($refund);
        }
    }

    /**
     * Returns the array of installments representing the payment plan for this payment.
     * @return Installment[]
     */
    public function getPaymentPlan(): array
    {
        return $this->paymentPlan;
    }

    /**
     * Returns the list of orders associated to that payment.
     * @return Order[]
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * Returns the list of refunds for that payment.
     * @return Refund[]
     */
    public function getRefunds(): array
    {
        return $this->refunds;
    }
}
