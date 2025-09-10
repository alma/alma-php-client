<?php

namespace Alma\API\Domain\Adapter;

interface CartAdapterInterface
{
    /**
     * Get the cart total in cents.
     *
     * @return int
     */
    public function getCartTotal(): int;

    /**
     * Empty the cart.
     *
     * @return void
     */
    public function emptyCart(): void;

    /**
     * Get the cart items categories.
     *
     * @return array An array of cart items categories.
     */
    public function getCartItemsCategories(): array;
}
