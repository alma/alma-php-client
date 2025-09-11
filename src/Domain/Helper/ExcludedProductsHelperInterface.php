<?php

namespace Alma\API\Domain\Helper;

use Alma\API\Domain\Adapter\CartAdapterInterface;
use Alma\API\Domain\Adapter\ProductAdapterInterface;

interface ExcludedProductsHelperInterface
{
    /**
     * Check if the widget can be displayed on the product page.
     *
     * @param ProductAdapterInterface $product The product to check.
     * @param array                   $excludedCategories List of excluded categories.
     *
     * @return bool True if the widget can be displayed, false otherwise.
     */
    public function canDisplayOnProductPage( ProductAdapterInterface $product, array $excludedCategories = array() ): bool;

    /**
     * Check if the widget can be displayed on the cart page.
     *
     * @param CartAdapterInterface $cartAdapter The cart adapter.
     * @param array                $excludedCategories List of excluded categories.
     *
     * @return bool True if the widget can be displayed, false otherwise.
     */
    public function canDisplayOnCartPage( CartAdapterInterface $cartAdapter, array $excludedCategories = array() ): bool;

    /**
     * Check if the widget can be displayed on the checkout page.
     *
     * @param CartAdapterInterface $cartAdapter The cart adapter.
     * @param array                $excludedCategories List of excluded categories.
     *
     * @return bool True if the widget can be displayed, false otherwise.
     */
    public function canDisplayOnCheckoutPage( CartAdapterInterface $cartAdapter, array $excludedCategories = array() ): bool;
}
