<?php

namespace Alma\API\DTO\MerchantData;

use Alma\API\DTO\DtoInterface;

class CmsInfoDto implements DtoInterface
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
    public function __construct(array $cmsInfoDataArray)
    {
        // Initialize values or set them to null if not available
        $this->cmsName = $cmsInfoDataArray['cms_name'] ?? '';
        $this->cmsVersion = $cmsInfoDataArray['cms_version'] ?? '';
        $this->thirdPartiesPlugins = $cmsInfoDataArray['third_parties_plugins'] ?? null;
        $this->themeName = $cmsInfoDataArray['theme_name'] ?? '';
        $this->themeVersion = $cmsInfoDataArray['theme_version'] ?? '';
        $this->languageName = $cmsInfoDataArray['language_name'] ?? '';
        $this->languageVersion = $cmsInfoDataArray['language_version'] ?? '';
        $this->almaPluginVersion = $cmsInfoDataArray['alma_plugin_version'] ?? '';
        $this->almaSdkVersion = $cmsInfoDataArray['alma_sdk_version'] ?? '';
        $this->almaSdkName = $cmsInfoDataArray['alma_sdk_name'] ?? '';
    }

    /**
     * @return array
     */
    public function toArray(): array
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
