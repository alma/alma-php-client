<?php

namespace Alma\API\Tests\Unit\DTO\MerchantData;

use Alma\API\Application\DTO\MerchantData\CmsFeaturesDto;
use Alma\API\Application\DTO\MerchantData\CmsInfoDto;
use Alma\API\Application\DTO\MerchantData\MerchantDataDto;
use PHPUnit\Framework\TestCase;

class MerchantDataDtoTest extends TestCase
{
    var MerchantDataDto $merchantDataDto;

    public function setUp(): void
    {
        $this->merchantDataDto = new MerchantDataDto();
    }

	public function testFormatIntegrationConfigurationPayload()
	{
		// Simulated input data for CmsInfo
		$cmsInfoData = [
			'cms_name' => 'WordPress',
			'cms_version' => '5.8',
			'third_parties_plugins' => [['name' => 'plugin1', 'version' => '1.0']],
			'themes' => [['name' => 'theme1', 'version' => '2.0']],
			'language_name' => 'PHP',
			'language_version' => '7.4',
			'alma_plugin_version' => '1.0.0',
			'alma_sdk_version' => '2.0.0',
			'alma_sdk_name' => 'Alma SDK'
		];
		$cmsInfo = new CmsInfoDto($cmsInfoData);

		// Simulated input data for CmsFeatures
		$cmsFeaturesData = [
			'alma_enabled' => true,
			'widget_cart_activated' => true,
			'widget_product_activated' => false,
			'used_fee_plans' => '{"plan":"A"}',
			'payment_method_position' => 1,
			'in_page_activated' => true,
			'log_activated' => false,
			'excluded_categories' => ['category1'],
			'excluded_categories_activated' => true,
			'specific_features' => [['name' => 'feature1']],
			'country_restriction' => ['FR', 'US'],
			'is_multisite' => false,
			'custom_widget_css' => true,
		];
		$cmsFeatures = new CmsFeaturesDto($cmsFeaturesData);

		// Call the method to be tested
		$result = $this->merchantDataDto->toArray($cmsInfo, $cmsFeatures);

		// Expected result in JSON format
		$expectedPayload = [
			'cms_info' => $cmsInfo->toArray(),
			'cms_features' => $cmsFeatures->toArray(),
		];

		// Assertion: Check if the output matches the expected JSON payload
		$this->assertEquals($expectedPayload, $result);
	}

	public function testFormatIntegrationConfigurationPayloadWithEmptyValues()
	{
		// CmsInfo with null or empty values
		$cmsInfoData = [
			'cms_name' => null,
			'cms_version' => '',
			'third_parties_plugins' => [],
			'themes' => [],
			'language_name' => null,
			'language_version' => '',
			'alma_plugin_version' => null,
			'alma_sdk_version' => null,
			'alma_sdk_name' => null
		];
		$cmsInfo = new CmsInfoDto($cmsInfoData);

		// CmsFeatures with null or empty values
		$cmsFeaturesData = [
			'alma_enabled' => null,
			'widget_cart_activated' => null,
			'widget_product_activated' => null,
			'used_fee_plans' => '',
			'payment_method_position' => null,
			'in_page_activated' => null,
			'log_activated' => null,
			'excluded_categories' => [],
			'excluded_categories_activated' => null,
			'specific_features' => [],
			'country_restriction' => [],
			'is_multisite' => null,
			'custom_widget_css' => null,
		];
		$cmsFeatures = new CmsFeaturesDto($cmsFeaturesData);

		// Call the method to be tested
		$result = $this->merchantDataDto->toArray($cmsInfo, $cmsFeatures);

		// Expected result in JSON format (should not include keys with null or empty values)
		$expectedPayload = [
			'cms_info' => $cmsInfo->toArray(),
			'cms_features' => $cmsFeatures->toArray(),
		];

		// Assertion: Check if the output matches the expected JSON payload
		$this->assertEquals($expectedPayload, $result);
	}
}
