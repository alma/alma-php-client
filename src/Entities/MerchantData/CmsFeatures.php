<?php

namespace Alma\API\Entities\MerchantData;

class CmsFeatures
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
     * @var array<array{name: string}>|null
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
    public function __construct($cmsFeaturesDataArray)
    {
        // Ensure values are properly initialized
        $this->almaEnabled = isset($cmsFeaturesDataArray['alma_enabled']) ? $cmsFeaturesDataArray['alma_enabled'] : null;
        $this->widgetCartActivated = isset($cmsFeaturesDataArray['widget_cart_activated']) ? $cmsFeaturesDataArray['widget_cart_activated'] : null;
        $this->widgetProductActivated = isset($cmsFeaturesDataArray['widget_product_activated']) ? $cmsFeaturesDataArray['widget_product_activated'] : null;
        $this->usedFeePlans = isset($cmsFeaturesDataArray['used_fee_plans']) ? $cmsFeaturesDataArray['used_fee_plans'] : null;
        $this->inPageActivated = isset($cmsFeaturesDataArray['in_page_activated']) ? $cmsFeaturesDataArray['in_page_activated'] : null;
        $this->logActivated = isset($cmsFeaturesDataArray['log_activated']) ? $cmsFeaturesDataArray['log_activated'] : null;
        $this->excludedCategories = isset($cmsFeaturesDataArray['excluded_categories']) ? $cmsFeaturesDataArray['excluded_categories'] : null;
        $this->paymentMethodPosition = isset($cmsFeaturesDataArray['payment_method_position']) ? $cmsFeaturesDataArray['payment_method_position'] : null;
        $this->specificFeatures = isset($cmsFeaturesDataArray['specific_features']) ? $cmsFeaturesDataArray['specific_features'] : null;
        $this->countryRestriction = isset($cmsFeaturesDataArray['country_restriction']) ? $cmsFeaturesDataArray['country_restriction'] : null;
        $this->isMultisite = isset($cmsFeaturesDataArray['is_multisite']) ? $cmsFeaturesDataArray['is_multisite'] : null;
        $this->customWidgetCss = isset($cmsFeaturesDataArray['custom_widget_css']) ? $cmsFeaturesDataArray['custom_widget_css'] : null;
    }

    /**
     * @return array
     */
    public function getProperties()
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