<?php

namespace Alma\API\Domain\Adapter;

interface CustomerAdapterInterface
{
    /**
     * Get the customer's shipping address.
     * @return ShippingAddressAdapterInterface|null
     */
    public function getCustomerShippingAddress(): ?ShippingAddressAdapterInterface;

    /**
     * Get the customer's billing address.
     * @return BillingAddressAdapterInterface|null
     */
    public function getCustomerBillingAddress(): ?BillingAddressAdapterInterface;

}
