<?php

namespace Alma\API\Domain\Helper;

interface ContextHelperInterface
{
    /**
     * Check if we are on the gateway settings page.
     *
     * @return bool True if we are on the gateway settings page, false otherwise.
     */
    public static function isGatewaySettingsPage(): bool;

    /**
     * Check if we are on the cart page.
     * @return bool
     */
    public static function isCartPage(): bool;

    /**
     * Check if we are on the checkout page.
     * @return bool
     */
    public static function isCheckoutPage(): bool;

    /**
     * Check if we are on the product page.
     * @return bool
     */
    public static function isProductPage(): bool;

    /**
     * Check if we are on the cart, product or checkout page.
     * @return bool
     */
    public static function isShop(): bool;

    /**
     * Defines if the current request is an admin request.
     *
     * @return bool True if the current request is an admin request, false otherwise.
     */
    public static function isAdmin(): bool;

    /**
     * Get the current language.
     *
     * @return string The current locale
     */
    public static function getLanguage(): string;

    /**
     * Get the current locale.
     *
     * @return string The current locale
     */
    public static function getLocale(): string;

    /**
     * Returns the current WooCommerce version.
     *
     * @return string
     */
    public static function getCmsVersion(): string;

    /**
     * Returns true if WooCommerce is active.
     *
     * @return bool
     */
    public static function isCmsLoaded(): bool;

	/**
	 * Get webhook url
	 *
	 * @param string $webhook Webhook name.
	 *
	 * @return string
	 */
    public static function getWebhookUrl( string $webhook ): string;

    /**
     * Get the current product price in cents.
     *
     * @return int
     */
    public static function getCurrentProductId(): int;
}
