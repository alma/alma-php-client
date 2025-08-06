<?php

namespace Alma\API\DTO;


use InvalidArgumentException;

class OrderDto implements DtoInterface {
    private ?string $merchantReference = null;
    private ?string $merchantUrl = null;
    private ?string $customerUrl = null;
    private ?string $comment = null;

    public function setMerchantReference(string $merchantReference): self {
        $this->merchantReference = $merchantReference;
        return $this;
    }

    public function setMerchantUrl(string $merchantUrl): self {
        if (!filter_var($merchantUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid merchant URL.");
        }
        $this->merchantUrl = $merchantUrl;
        return $this;
    }

    public function setCustomerUrl(string $customerUrl): self {
        if (!filter_var($customerUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid customer URL.");
        }
        $this->customerUrl = $customerUrl;
        return $this;
    }

    public function setComment(?string $comment): self {
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
            'merchant_reference' => $this->merchantReference,
            'merchant_url'       => $this->merchantUrl,
            'customer_url'       => $this->customerUrl,
            'comment'            => $this->comment,
        ], function($value) {
            return $value !== null && $value !== '' && !(is_array($value) && empty($value));
        });
    }
}
