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

namespace Alma\API\Entities;

class Payment extends Base
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

    /** @var int Creation UNIX timestamp */
    public $created;

    /** @var string URL of that payment's page to which the customer should be redirected */
    public $url;

    /** @var string State of the payment (see above STATE_PAID / STATE_IN_PROGRESS / ...) */
    public $state;

    /** @var int Purchase amount, in cents */
    public $purchase_amount;

    /** @var int Fees to be paid by the customer, in cents */
    public $customer_fee;

    /** @var int Interests to be paid by the customer, in cents */
    public $customer_interest;

    /** @var int Fees paid by the merchant, in cents */
    public $merchant_target_fee;

    /** @var int Number of installments for this payment */
    public $installments_count;

    /** @var int Number of days the payment was deferred for */
    public $deferred_days;

    /** @var int Number of months the payment was deferred for */
    public $deferred_months;

    /**
     * @var Instalment[] Array of installments, representing the payment plan for this payment.
     * Might include more than $installments_count installments in some cases.
     */
    public $payment_plan;

    /** @var string URL the customer is sent back to once the payment is complete */
    public $return_url;

    /** @var array Custom data provided at creation time */
    public $custom_data;

    /** @var Order[] List of orders associated to that payment */
    public $orders;

    /** @var Refund[] List of refunds for that payment */
    public $refunds;

    /** @var array Customer representation */
    public $customer;

    /** @var array Billing address representation */
    public $billing_address;

    /** @var bool If is a payment with trigger or not */
    public $deferred_trigger;

    /** @var string|null Description given at payment creation */
    public $deferred_trigger_description;

    /** @var int|null Timestamp or NULL if not already applied */
    public $deferred_trigger_applied;

    /** @var int|null Timestamp or NULL if not expired */
    public $expired_at;

    /**
     * @param array $attributes
     */
    public function __construct($attributes)
    {
        // Manually process `payment_plan` to create Instalment instances
        if (array_key_exists('payment_plan', $attributes)) {
            $this->payment_plan = array();

            foreach ($attributes['payment_plan'] as $instalment) {
                $this->payment_plan[] = new Instalment($instalment);
            }

            unset($attributes['payment_plan']);
        }

        if (array_key_exists('orders', $attributes)) {
            $this->orders = array();

            foreach ($attributes['orders'] as $order) {
                $this->orders[] = new Order($order);
            }

            unset($attributes['orders']);
        }

        if (array_key_exists('refunds', $attributes)) {
            $this->refunds = array();

            foreach ($attributes['refunds'] as $refund) {
                $this->refunds[] = new Refund($refund);
            }

            unset($attributes['refunds']);
        }

        parent::__construct($attributes);
    }
}
