<?php

namespace Alma\API\Domain\Helper;

use Alma\API\Domain\Adapter\OrderAdapterInterface;

interface NavigationHelperInterface
{
    /**
     * Redirects to the return URL after payment.
     * This method is used to redirect the user to the return URL after a successful payment.
     * It retrieves the return URL from the payment method and redirects the user to that URL.
     * If the return URL is not set, it falls back to the cart URL.
     *
     * @param OrderAdapterInterface $order The order object containing the payment method and return URL.
     *
     * @return void
     */
    public function redirectAfterPayment( OrderAdapterInterface $order ): void;

    /**
     * Redirect to the cart page with an optional message.
     *
     * @param string|null $message The message to display on the cart page.
     */
    public function redirectToCart( $message = null ): void;
}
