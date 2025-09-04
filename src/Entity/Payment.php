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

/**
 * Class Payment
 * @package Alma\API\Entity
 * @link https://docs.almapay.com/reference/payment
 */
class Payment extends AbstractEntity
{
    /**
     * @var string Payment is ongoing
     * @noinspection PhpUnused Used by implementations
     */
    const STATE_IN_PROGRESS = 'in_progress';

    /**
     * @var string Payment has been fully paid, either at once after being scored negatively, or after all installments
     *              have been paid for. Note that by extension, a payment that has no amount due left after partial or
     *              total refunds will be considered PAID as well.
     * @noinspection PhpUnused Used by implementations
     */
    const STATE_PAID = 'paid';

    /**
     * @var string Payment has been cancelled, automatically because the payment amount and the order amount did not match.
     * @noinspection PhpUnused Used by implementations
     */
    const FRAUD_AMOUNT_MISMATCH = 'amount_mismatch';

    /**
     * @var string Payment has been cancelled, either by the merchant or automatically by Alma because the payment was not completed
     *              within the time limit.
     * @noinspection PhpUnused Used by implementations
     */
    const FRAUD_STATE_ERROR = 'state_error';

    /** @var string Payment ID */
    protected string $id;

    /** @var string Merchant name attached to the Payment */
    protected string $merchantName;

    /** @var string Payment status. */
    protected string $state;

    /** @var int Payment creation date */
    protected int $createdAt;

    /** @var int Date of last modification of the Payment */
    protected int $updatedAt;

    /** @var int Cart amount, excluding Alma fees */
    protected int $purchaseAmount;

    /**
     * @var string|null Payment type: P1X, P1X_D+30, P3X, P10X, etc…
     * @deprecated That's not a kind anymore. Use payment_plan instead.
     */
    protected ?string $kind;

    /** @var Installment[] Array of installments, representing the payment plan for this payment. */
    protected array $paymentPlan;

    /** @var array Information about the customer who made the payment */
    protected array $customer;

    /** @var Order[] List of orders associated to that payment */
    protected array $orders;

    /** `
     * @var string|null Origin of the payment (e.g., "online", "online_in_page", etc.)
     * @link https://docs.almapay.com/reference/origine-des-paiements
     */
    protected ?string $origin;

    /** @var int Number of months prior to the first installment. */
    protected int $deferredMonths;

    /** @var int Number of days before the first installment. */
    protected int $deferredDays;

    /** @var string|null Indicates the country in which the purchase is taking place from which general terms and conditions of sale result. */
    protected ?string $transactionCountry;

    /** @var Refund[] List of refunds for that payment */
    protected array $refunds;

    /** Mapping of required fields */
    protected array $requiredFields = [
        'id'                 => 'id',
        'merchantName'       => 'merchant_name',
        'state'              => 'state',
        'createdAt'          => 'created',
        'updatedAt'          => 'updated',
        'purchaseAmount'     => 'purchase_amount',
        'paymentPlan'        => 'payment_plan',
        'customer'           => 'customer',
        'orders'             => 'orders',
        'deferredMonths'     => 'deferred_months',
        'deferredDays'       => 'deferred_days',
        'refunds'            => 'refunds',
    ];

    /** Mapping of optional fields */
    protected array $optionalFields = [
        'kind'               => 'kind',
        'origin'             => 'origin',
        'transactionCountry' => 'transaction_country',
    ];

    /**
     * @param array $paymentData
     * @throws ParametersException
     * @noinspection PhpMissingParentConstructorInspection Parent constructor is intentionally not called
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
     * Returns the payment ID.
     * @return string
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns the merchant name attached to the Payment.
     * @return string
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getMerchantName(): string
    {
        return $this->merchantName;
    }

    /**
     * Returns the payment status.
     * @return string
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Returns the payment creation date as a timestamp.
     * @return int
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Returns the date of last modification of the Payment as a timestamp.
     * @return int
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getUpdatedAt(): int
    {
        return $this->updatedAt;
    }

    /**
     * Returns the cart amount, excluding Alma fees, in cents.
     * @return int
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getPurchaseAmount(): int
    {
        return $this->purchaseAmount;
    }

    /**
     * Returns the payment type: P1X, P1X_D+30, P3X, P10X, etc…
     * @return string|null
     * @deprecated That's not a kind anymore. Use getPaymentPlan() instead.
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getKind(): ?string
    {
        return $this->kind;
    }

    /**
     * Returns the array of installments representing the payment plan for this payment.
     * @return Installment[]
     * @noinspection PhpUnusedFunctionInspection
     */
    public function getPaymentPlan(): array
    {
        return $this->paymentPlan;
    }

    /**
     * Returns the information about the customer who made the payment.
     * @return array
     * @noinspection PhpUnusedFunctionInspection
     */
    public function getCustomer(): array
    {
        return $this->customer;
    }

    /**
     * Returns the list of orders associated to that payment.
     * @return Order[]
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getOrders(): array
    {
        return $this->orders;
    }

    /**
     * Returns the origin of the payment (e.g., "online", "online_in_page", etc.)
     * @return string|null
     * @link https://docs.almapay.com/reference/origine-des-paiements
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    /**
     * Returns the number of months prior to the first installment.
     * @return int
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getDeferredMonths(): int
    {
        return $this->deferredMonths;
    }

    /**
     * Returns the number of days before the first installment.
     * @return int
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getDeferredDays(): int
    {
        return $this->deferredDays;
    }

    /**
     * Returns the country in which the purchase is taking place from which general terms and conditions of sale result.
     * @return string|null
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getTransactionCountry(): ?string
    {
        return $this->transactionCountry;
    }

    /**
     * Returns the list of refunds for that payment.
     * @return Refund[]
     * @noinspection PhpUnusedFunctionInspection Used by implementations
     */
    public function getRefunds(): array
    {
        return $this->refunds;
    }
}
