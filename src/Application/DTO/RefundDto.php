<?php

namespace Alma\API\Application\DTO;

use InvalidArgumentException;

class RefundDto implements DtoInterface {
    private ?int $amount = null;
    private string $merchantReference = '';
    private string $comment = '';

    /**
     * @param int $amount
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setAmount(int $amount): self {
        if ($amount < 0) {
            throw new InvalidArgumentException("Refund amount cannot be negative.");
        }
        $this->amount = $amount;
        return $this;
    }

    /**
     * Set the Merchant Reference.
     * This is a unique identifier for the refund, typically used to track the refund in the merchant's system.
     *
     * @param string $merchantReference
     * @return $this
     */
    public function setMerchantReference(string $merchantReference): self {
        $this->merchantReference = $merchantReference;
        return $this;
    }

    /**
     * Set the comment for the refund.
     * This can be used to provide additional information about the refund.
     *
     * @param string $comment
     * @return $this
     */
    public function setComment(string $comment): self {
        $this->comment = $comment;
        return $this;
    }

    /**
     * Convert the Dto to an array.
     * This method prepares the DTO for serialization or API requests.
     *
     * @return array
     */
    public function toArray(): array {
        return array_filter([
            'amount'             => $this->amount,
            'merchant_reference' => $this->merchantReference,
            'comment'            => $this->comment
        ], function($value) {
            return $value !== null && $value !== '' && !(is_array($value) && empty($value));
        });
    }
}
