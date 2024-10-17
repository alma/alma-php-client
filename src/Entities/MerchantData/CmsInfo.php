<?php

namespace Alma\API\Entities\MerchantData;

class CmsInfo
{
	/**
	 * @var string|null
	 */
	private $cmsName;

	/**
	 * @var string|null
	 */
	private $cmsVersion;

	/**
	 * @var array<array{name: string, version: string}>
	 */
	private $thirdPartiesPlugins;

	/**
	 * @var array<array{name: string, version: string}>
	 */
	private $themes;

	/**
	 * @var string|null
	 */
	private $languageName;

	/**
	 * @var string|null
	 */
	private $languageVersion;

	/**
	 * @var string|null
	 */
	private $almaPluginVersion;

	/**
	 * @var string|null
	 */
	private $almaSdkVersion;

	/**
	 * @var string|null
	 */
	private $almaSdkName;

	/**
	 * CmsInfo constructor.
	 * @param array $cmsInfoDataArray
	 */
	public function __construct($cmsInfoDataArray)
	{
		// Initialize values or set them to null if not available
		$this->cmsName = isset($cmsInfoDataArray['cms_name']) ? $cmsInfoDataArray['cms_name'] : null;
		$this->cmsVersion = isset($cmsInfoDataArray['cms_version']) ? $cmsInfoDataArray['cms_version'] : null;
		$this->thirdPartiesPlugins = isset($cmsInfoDataArray['third_parties_plugins']) ? $cmsInfoDataArray['third_parties_plugins'] : [];
		$this->themes = isset($cmsInfoDataArray['themes']) ? $cmsInfoDataArray['themes'] : [];
		$this->languageName = isset($cmsInfoDataArray['language_name']) ? $cmsInfoDataArray['language_name'] : null;
		$this->languageVersion = isset($cmsInfoDataArray['language_version']) ? $cmsInfoDataArray['language_version'] : null;
		$this->almaPluginVersion = isset($cmsInfoDataArray['alma_plugin_version']) ? $cmsInfoDataArray['alma_plugin_version'] : null;
		$this->almaSdkVersion = isset($cmsInfoDataArray['alma_sdk_version']) ? $cmsInfoDataArray['alma_sdk_version'] : null;
		$this->almaSdkName = isset($cmsInfoDataArray['alma_sdk_name']) ? $cmsInfoDataArray['alma_sdk_name'] : null;
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
			'themes' => $this->themes,
			'language_name' => $this->languageName,
			'language_version' => $this->languageVersion,
			'alma_plugin_version' => $this->almaPluginVersion,
			'alma_sdk_version' => $this->almaSdkVersion,
			'alma_sdk_name' => $this->almaSdkName,
		], function($value) {
			// Keep only values that are not null and not empty
			return !is_null($value) && $value !== '';
		});
	}
}