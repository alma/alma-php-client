<?php

namespace Alma\API\Application\DTO;


use InvalidArgumentException;

class EligibilityDto implements DtoInterface {

    private ?int $purchaseAmount = null;
    private array $queries = [];
    private ?string $origin = null;
    private ?array $billingAddress = [];
    private ?array $shippingAddress = [];

    /**
     * Construct DTO and Set the total purchase amount.
     *
     * @param int $purchaseAmount The total amount of the purchase in cents.
     *
     * @throws InvalidArgumentException if the purchase amount is negative.
     */
    public function __construct(int $purchaseAmount) {
        if ($purchaseAmount < 0) {
            throw new InvalidArgumentException("Purchase amount cannot be negative.");
        }
        $this->purchaseAmount = $purchaseAmount;
    }

    /**
     * Set the eligibility queries. Can be empty and all Eligibilities will be returned.
     * @param array $queries
     * @return $this
     */
    public function setQueries(array $queries): self {
        $this->queries = $queries;
        return $this;
    }

    /**
     * Set the origin of the eligibility request.
     *
     * @param string $origin The origin value, must be either 'online' or 'online_in_page'.
     * @return $this
     * @throws InvalidArgumentException if the origin value is invalid.
     */
    public function setOrigin(string $origin): self {
        if (!in_array($origin, [PaymentDto::ORIGIN_ONLINE, PaymentDto::ORIGIN_ONLINE_IN_PAGE])) {
            throw new InvalidArgumentException("Invalid origin value.");
        }
        $this->origin = $origin;
        return $this;
    }

    /**
     * Set the billing address.
     *
     * @param AddressDto $address The billing address DTO.
     * @return $this
     */
    public function setBillingAddress(AddressDto $address): self {
        $this->billingAddress = $address->toArray();
        return $this;
    }

    /**
     * Set the shipping address.
     *
     * @param AddressDto $address The shipping address DTO.
     * @return $this
     */
    public function setShippingAddress(AddressDto $address): self {
        $this->shippingAddress = $address->toArray();
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
            'purchase_amount'      => $this->purchaseAmount,
            'queries'              => $this->queries,
            'origin'               => $this->origin,
            'billing_address'      => $this->billingAddress,
            'shipping_address'     => $this->shippingAddress,
        ], function($value) {
            return $value !== null && $value !== '' && !(is_array($value) && empty($value));
        });
    }
}
