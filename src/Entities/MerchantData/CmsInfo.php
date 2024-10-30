<?php

namespace Alma\API\Entities\MerchantData;

class CmsInfo
{
    /**
     * @var string
     */
    private $cmsName;

    /**
     * @var string
     */
    private $cmsVersion;

    /**
     * @var array<array{name: string, version: string}>|null
     */
    private $thirdPartiesPlugins;

    /**
     * @var string
     */
    private $languageName;

    /**
     * @var string
     */
    private $languageVersion;

    /**
     * @var string
     */
    private $almaPluginVersion;

    /**
     * @var string
     */
    private $almaSdkVersion;

    /**
     * @var string
     */
    private $almaSdkName;

    /**
     * @var string | null
     */
    private $themeName;

    /**
     * @var null|string
     */
    private $themeVersion;

    /**
     * CmsInfo constructor.
     * @param array $cmsInfoDataArray
     */
    public function __construct($cmsInfoDataArray)
    {
        // Initialize values or set them to null if not available
        $this->cmsName = isset($cmsInfoDataArray['cms_name']) ? $cmsInfoDataArray['cms_name'] : '';
        $this->cmsVersion = isset($cmsInfoDataArray['cms_version']) ? $cmsInfoDataArray['cms_version'] : '';
        $this->thirdPartiesPlugins = isset($cmsInfoDataArray['third_parties_plugins']) ? $cmsInfoDataArray['third_parties_plugins'] : null;
        $this->themeName = isset($cmsInfoDataArray['theme_name']) ? $cmsInfoDataArray['theme_name'] : '';
        $this->themeVersion = isset($cmsInfoDataArray['theme_version']) ? $cmsInfoDataArray['theme_version'] : '';
        $this->languageName = isset($cmsInfoDataArray['language_name']) ? $cmsInfoDataArray['language_name'] : '';
        $this->languageVersion = isset($cmsInfoDataArray['language_version']) ? $cmsInfoDataArray['language_version'] : '';
        $this->almaPluginVersion = isset($cmsInfoDataArray['alma_plugin_version']) ? $cmsInfoDataArray['alma_plugin_version'] : '';
        $this->almaSdkVersion = isset($cmsInfoDataArray['alma_sdk_version']) ? $cmsInfoDataArray['alma_sdk_version'] : '';
        $this->almaSdkName = isset($cmsInfoDataArray['alma_sdk_name']) ? $cmsInfoDataArray['alma_sdk_name'] : '';
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        // Use array_filter with ARRAY_FILTER_USE_BOTH to remove null or empty values
        return array_filter([
            'cms_name' => $this->cmsName,
            'cms_version' => $this->cmsVersion,
            'third_parties_plugins' => $this->thirdPartiesPlugins,
            'theme_name' => $this->themeName,
            'theme_version' => $this->themeVersion,
            'language_name' => $this->languageName,
            'language_version' => $this->languageVersion,
            'alma_plugin_version' => $this->almaPluginVersion,
            'alma_sdk_version' => $this->almaSdkVersion,
            'alma_sdk_name' => $this->almaSdkName,
        ], function ($value) {
            // Keep only values that are not null and not empty
            // But keep false or 0 values
            return !is_null($value) && $value !== '';
        });
    }
}