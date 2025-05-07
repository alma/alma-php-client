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

class FeePlan extends Base implements PaymentPlanInterface
{
    use PaymentPlanTrait;

    const KIND_GENERAL = 'general';
    const KIND_POS = 'pos';

    /** @var int Installments count this fee plan applies to*/
    public $installmentsCount;

    /** @var string Kind of payments this fee plan applies to (see kinds above, most likely KIND_GENERAL) */
    public $kind;

    /** @var int Number of deferred months this fee plan applies to */
    public $deferredMonths;

    /** @var int Number of deferred days this fee plan applies to */
    public $deferredDays;

    /** @var int Number of deferred trigger limit days this fee plan applies to */
    public $deferredTriggerLimitDays;

    /** @var int Maximum purchase amount allowed for this fee plan */
    public $maxPurchaseAmount;

    /** @var int Minimum purchase amount allowed for this fee plan */
    public $minPurchaseAmount;

    /** @var int Is this fee plan allowed/enabled? */
    public $allowed;

    /** @var int Percentage of fees in bps paid for by the merchant (100bps = 1%) */
    public $merchantFeeVariable;

    /** @var int Fixed fees in cents paid for by the merchant */
    public $merchantFeeFixed;

    /** @var int Percentage of fees in bps paid for by the customer (100bps = 1%) */
    public $customerFeeVariable;

    /** @var int Percentage of lending rate in bps used to calculate the fee plan interest paid by the customer (100bps = 1%) */
    public $customerLendingRate;

    /** @var int Fixed fees in cents paid for by the customer */
    public $customerFeeFixed;

    public function getDeferredDays(): int
    {
        return $this->deferredDays;
    }

    public function getDeferredMonths(): int
    {
        return $this->deferredMonths;
    }

    public function getDeferredTriggerLimitDays(): int
    {
        return $this->deferredTriggerLimitDays;
    }

    public function getInstallmentsCount(): int
    {
        return $this->installmentsCount;
    }

    public function getKind(): string
    {
        return $this->kind;
    }
}
