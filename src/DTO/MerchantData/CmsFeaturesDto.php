<?php

namespace Alma\API\DTO\MerchantData;

use Alma\API\DTO\DtoInterface;

class CmsFeaturesDto  implements DtoInterface
{
    /**
     * @var bool | null
     */
    private $almaEnabled;

    /**
     * @var bool | null
     */
    private $widgetCartActivated;

    /**
     * @var bool | null
     */
    private $widgetProductActivated;

    /**
     * @var array|null
     */
    private $usedFeePlans;

    /**
     * @var int | null
     */
    private $paymentMethodPosition;

    /**
     * @var bool | null
     */
    private $inPageActivated;

    /**
     * @var bool | null
     */
    private $logActivated;

    /**
     * @var string[]|null
     */
    private $excludedCategories;

    /**
     * @var string[]|null
     */
    private $specificFeatures;

    /**
     * @var string[]|null
     */
    private $countryRestriction;

    /**
     * @var bool | null
     */
    private $isMultisite;

    /**
     * @var bool | null
     */
    private $customWidgetCss;

    /**
     * CmsFeatures constructor.
     * @param array $cmsFeaturesDataArray
     */
    public function __construct(array $cmsFeaturesDataArray)
    {
        // Ensure values are properly initialized
        $this->almaEnabled = $cmsFeaturesDataArray['alma_enabled'] ?? null;
        $this->widgetCartActivated = $cmsFeaturesDataArray['widget_cart_activated'] ?? null;
        $this->widgetProductActivated = $cmsFeaturesDataArray['widget_product_activated'] ?? null;
        $this->usedFeePlans = $cmsFeaturesDataArray['used_fee_plans'] ?? null;
        $this->inPageActivated = $cmsFeaturesDataArray['in_page_activated'] ?? null;
        $this->logActivated = $cmsFeaturesDataArray['log_activated'] ?? null;
        $this->excludedCategories = $cmsFeaturesDataArray['excluded_categories'] ?? null;
        $this->paymentMethodPosition = $cmsFeaturesDataArray['payment_method_position'] ?? null;
        $this->specificFeatures = $cmsFeaturesDataArray['specific_features'] ?? null;
        $this->countryRestriction = $cmsFeaturesDataArray['country_restriction'] ?? null;
        $this->isMultisite = $cmsFeaturesDataArray['is_multisite'] ?? null;
        $this->customWidgetCss = $cmsFeaturesDataArray['custom_widget_css'] ?? null;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        // Use array_filter with ARRAY_FILTER_USE_BOTH to remove null or empty values
        return array_filter([
            'alma_enabled' => $this->almaEnabled,
            'widget_cart_activated' => $this->widgetCartActivated,
            'widget_product_activated' => $this->widgetProductActivated,
            'used_fee_plans' => $this->usedFeePlans,
            'in_page_activated' => $this->inPageActivated,
            'log_activated' => $this->logActivated,
            'excluded_categories' => $this->excludedCategories,
            'payment_method_position' => $this->paymentMethodPosition,
            'specific_features' => $this->specificFeatures,
            'country_restriction' => $this->countryRestriction,
            'is_multisite' => $this->isMultisite,
            'custom_widget_css' => $this->customWidgetCss,
        ], function ($value) {
            // Keep only values that are not null and not empty
            // But keep false or 0 values
            return !is_null($value) && $value !== '';
        });
    }
}
