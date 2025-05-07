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

namespace Alma\API\Payloads;

use Alma\API\Exceptions\ParametersException;

class Refund
{
    /* @var string */
    private string $id;

    /* @var int|null */
    private ?int $amount = null;

    /* @var string */
    private string $merchantReference = '';

    /* @var string */
    private string $comment = '';

    /**
     * The Refund object create a payload to give to the refund endpoint
     *
     * @param string $id payment_id
     * @param int|null $amount the amount to refund, null means all
     * @param string $merchantReference a reference for the merchant
     * @param string $comment
     * @return Refund
     *
     * @throws ParametersException
     */
    public static function create(string $id, ?int $amount = 0, string $merchantReference = "", string $comment = ""): Refund
    {
        if ($id === '') {
            throw new ParametersException('Refund Error. Payment Id can\'t be empty.');
        }

        if ($amount === 0) {
            throw new ParametersException('Refund warning, the refund is zero');
        }

        if ($amount < 0) {
            throw new ParametersException('Refund Error. You can\'t refund a negative amount.');
        }

        $refundPayload = new self($id);
        if (!is_null($amount)) {
            $refundPayload->setAmount($amount);
        }
        $refundPayload->setMerchantReference($merchantReference);
        $refundPayload->setComment($comment);

        return $refundPayload;
    }

    /**
     * @param string $id
     */
    public function __construct(string $id) {
        $this->setId($id);
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id) {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int|null $amount
     */
    public function setAmount(?int $amount) {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getMerchantReference(): string
    {
        return $this->merchantReference;
    }

    /**
     * @param string $merchantReference
     */
    public function setMerchantReference(string $merchantReference) {
        $this->merchantReference = $merchantReference;
    }

    /**
     * @return string
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     */
    public function setComment(string $comment) {
        $this->comment = $comment;
    }

    /**
     * @return array
     */
    public function getRequestBody(): array
    {
        $requestBody = [
            "merchant_reference" => $this->getMerchantReference(),
            "comment" => $this->getComment(),
        ];

        if ($this->getAmount() !== null && $this->getAmount() > 0) {
            $requestBody["amount"] = $this->getAmount();
        }

        return $requestBody;
    }
}
