<?php

namespace Alma\API\Domain\Helper;

use Alma\API\Domain\Entity\FeePlanList;

interface WidgetHelperInterface
{
    /**
     * Display the cart widget with the given price.
     *
     * @param string      $environment The API environment (live or test).
     * @param string      $merchantId The merchant ID.
     * @param int         $price The total price of the cart in cents.
     * @param FeePlanList $feePlanList The list of fee plans.
     * @param string      $language The language code (e.g., 'en', 'fr', etc.).
     * @param bool        $display_widget Whether to display the widget or not.
     */
    public function displayCartWidget(
        string $environment,
        string $merchantId, int $price,
        FeePlanList $feePlanList,
        string $language,
        bool $display_widget = false
    );

    /**
     * Display the product widget with the given price.
     *
     * @param string      $environment The API environment (live or test).
     * @param string      $merchantId The merchant ID.
     * @param int         $price The price of the product in cents.
     * @param FeePlanList $feePlanList The list of fee plans.
     * @param string      $language The language code (e.g., 'en', 'fr', etc.).
     * @param bool        $display_widget Whether to display the widget or not.
     */
    public function displayProductWidget(
        string $environment,
        string $merchantId,
        int $price,
        FeePlanList $feePlanList,
        string $language,
        bool $display_widget = false
    );
}
