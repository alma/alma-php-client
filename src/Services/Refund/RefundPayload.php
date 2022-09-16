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
 */

namespace Alma\API\Services\Refund;

use Alma\API\ParamsError;
use Alma\API\Services\AbstractPayload;

class RefundPayload extends AbstractPayload
{
    /* @param string */
    protected $id;

    /* @param int */
    private $amount = 0;

    /* @param string */
    private $merchantReference = '';

    /* @param string */
    private $comment = '';

    public function getId() {
        return $this->id;
    }

    /**
     * The Refund object create a payload to give to the refund endpoint
     *
     * @param string $id                payment_id
     * @param int    $amount            the amount to refund, 0 means all
     * @param string $merchantReference a reference for the merchant
     * @param string $comment
     *
     * @return RefundPayload
     *
     */
    public static function create($id, $amount = 0, $merchantReference = "", $comment = "")
    {
        $refundPayload = new self($id);
        if ($amount !== 0) {
            $refundPayload->setAmount($amount);
        }
        $refundPayload->setMerchantReference($merchantReference);
        $refundPayload->setComment($comment);

        return $refundPayload;
    }

    /**
     * @param string
     */
    public function __construct($id)
    {
        $this->setId($id);
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @param string $merchantReference
     */
    public function setMerchantReference($merchantReference)
    {
        $this->merchantReference = $merchantReference;
    }

    /**
     * @param string $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return bool
     * @throws ParamsError
     */
    public function validate() {
        if (!isset($this->id) || $this->id == "") {
            throw new ParamsError("Invalid Refund Request: some mandatory field is missing: <id>");
        }

        if (isset($this->amount) && $this->amount < 0) {
            throw new ParamsError("Invalid Refund Request: <amount> should be > 0");
        }
        return true;
    }

    /**
     * @return array
     */
    public function toPayload()
    {
        $requestBody = [
            "merchant_reference" => $this->merchantReference,
            "comment" => $this->comment,
        ];
        if ($this->amount > 0) {
            $requestBody["amount"] = $this->amount;
        }
        return $requestBody;
    }

    public function getUrl($path) {
        return sprintf($path, $this->getId());
    }
}
