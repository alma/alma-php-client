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


    /** @var int  Amount already refunded for the payment */
    protected int $amountRefunded;

    /** @var array Custom data provided when creating the payment */
    protected array $customData;

    /** @var int Customer fee for payment */
    protected int $customerFee;

    /** @var int Customer interest for payment */
    protected int $customerInterest;

    /** @var int Number of days before the first installment. */
    protected int $deferredDays;

    /** @var int Number of months prior to the first installment. */
    protected int $deferredMonths;

    /** @var int|null Payment expiration date as a timestamp, if any */
    protected ?int $expiredAt;

    /** @var string Payment ID */
    protected string $id;

    /** @var int Number of installments for that payment */
    protected int $installmentsCount;

    /** @var string|null Payment plan kind: P1X, P1X_D+30, P3X, P10X, etc… */
    protected ?string $kind;

    /** @var int Cart amount, excluding Alma fees */
    protected int $purchaseAmount;

    /** @var string Payment status. */
    protected string $state;

    /** @var string Payment URL */
    protected string $url;

    /** Mapping of required fields */
    protected array $requiredFields = [
        'amountRefunded'     => 'amount_already_refunded',
        'customData'         => 'custom_data',
        'customerFee'        => 'customer_fee',
        'customerInterest'   => 'customer_interest',
        'deferredDays'       => 'deferred_days',
        'deferredMonths'     => 'deferred_months',
        'expiredAt'          => 'expired_at',
        'id'                 => 'id',
        'installmentsCount'  => 'installments_count',
        'kind'               => 'kind',
        'purchaseAmount'     => 'purchase_amount',
        'state'              => 'state',
        'url'                => 'url',
    ];

    /** Mapping of optional fields */
    protected array $optionalFields = [];


    /**
     * Returns the amount already refunded for the payment, in cents.
     * @return int
     */
    public function getAmountRefunded(): int
    {
        return $this->amountRefunded;
    }

    /**
     * Returns the custom data provided when creating the payment.
     * @return array
     */
    public function getCustomData(): array
    {
        return $this->customData;
    }

    /**
     * Returns the customer fee for payment, in cents.
     * @return int
     */
    public function getCustomerFee(): int
    {
        return $this->customerFee;
    }

    /**
     * Returns the customer interest for payment, in cents.
     * @return int
     */
    public function getCustomerInterest(): int
    {
        return $this->customerInterest;
    }


    /**
     * Returns the number of days before the first installment.
     * @return int
     */
    public function getDeferredDays(): int
    {
        return $this->deferredDays;
    }

    /**
     * Returns the number of months prior to the first installment.
     * @return int
     */
    public function getDeferredMonths(): int
    {
        return $this->deferredMonths;
    }

    /**
     * Returns the payment expiration date as a timestamp, if any.
     * @return int|null
     */
    public function getExpiredAt(): ?int
    {
        return $this->expiredAt;
    }

    /**
     * Returns the payment external ID.
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns the number of installments for that payment.
     * @return int
     */
    public function getInstallmentsCount(): int
    {
        return $this->installmentsCount;
    }

    /**
     * Returns the payment type: P1X, P1X_D+30, P3X, P10X, etc…
     * @return string|null
     */
    public function getKind(): ?string
    {
        return $this->kind;
    }

    /**
     * Returns the cart amount, excluding Alma fees, in cents.
     * @return int
     */
    public function getPurchaseAmount(): int
    {
        return $this->purchaseAmount;
    }

    /**
     * Returns the payment status.
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Returns the payment URL.
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }


}
