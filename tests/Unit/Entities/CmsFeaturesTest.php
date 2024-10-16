<?php

namespace Unit\Entities;

use Alma\API\Entities\MerchantData\CmsFeatures;
use PHPUnit\Framework\TestCase;

class CmsFeaturesTest extends TestCase
{
	public function testConstructorSetsValuesCorrectly()
	{
		$data = [
			'alma_enabled' => true,
			'widget_cart_activated' => true,
			'widget_product_activated' => false,
			'used_fee_plans' => 'Plan A',
			'payment_method_position' => 1,
			'in_page_activated' => true,
			'log_activated' => false,
			'excluded_categories' => ['category1', 'category2'],
			'excluded_categories_activated' => true,
			'specific_features' => ['feature1', 'feature2'],
			'country_restriction' => ['FR', 'US'],
			'is_multisite' => false,
			'custom_widget_css' => true,
		];

		$cmsFeatures = new CmsFeatures($data);

		$this->assertEquals(true, $cmsFeatures->getProperties()['alma_enabled']);
		$this->assertEquals(true, $cmsFeatures->getProperties()['widget_cart_activated']);
		$this->assertEquals(false, $cmsFeatures->getProperties()['widget_product_activated']);
		$this->assertEquals('Plan A', $cmsFeatures->getProperties()['used_fee_plans']);
		$this->assertEquals(1, $cmsFeatures->getProperties()['payment_method_position']);
		$this->assertEquals(true, $cmsFeatures->getProperties()['in_page_activated']);
		$this->assertEquals(false, $cmsFeatures->getProperties()['log_activated']);
		$this->assertEquals(['category1', 'category2'], $cmsFeatures->getProperties()['excluded_categories']);
		$this->assertEquals(true, $cmsFeatures->getProperties()['excluded_categories_activated']);
		$this->assertEquals(['feature1', 'feature2'], $cmsFeatures->getProperties()['specific_features']);
		$this->assertEquals(['FR', 'US'], $cmsFeatures->getProperties()['country_restriction']);
		$this->assertEquals(false, $cmsFeatures->getProperties()['is_multisite']);
		$this->assertEquals(true, $cmsFeatures->getProperties()['custom_widget_css']);
	}

	public function testConstructorHandlesNullValuesCorrectly()
	{
		$data = [
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

		$cmsFeatures = new CmsFeatures($data);
		$properties = $cmsFeatures->getProperties();

		$this->assertArrayNotHasKey('alma_enabled', $properties);
		$this->assertArrayNotHasKey('widget_cart_activated', $properties);
		$this->assertArrayNotHasKey('widget_product_activated', $properties);
		$this->assertArrayNotHasKey('used_fee_plans', $properties);
		$this->assertArrayNotHasKey('payment_method_position', $properties);
		$this->assertArrayNotHasKey('in_page_activated', $properties);
		$this->assertArrayNotHasKey('log_activated', $properties);
		$this->assertEquals([], $properties['excluded_categories']); // Should be an empty array
		$this->assertArrayNotHasKey('excluded_categories_activated', $properties);
		$this->assertEquals([], $properties['specific_features']); // Should be an empty array
		$this->assertEquals([], $properties['country_restriction']); // Should be an empty array
		$this->assertArrayNotHasKey('is_multisite', $properties);
		$this->assertArrayNotHasKey('custom_widget_css', $properties);
	}

	public function testGetPropertiesFiltersOutNullAndEmptyValues()
	{
		$data = [
			'alma_enabled' => true,
			'widget_cart_activated' => null,
			'widget_product_activated' => null,
			'used_fee_plans' => 'Plan B',
			'payment_method_position' => null,
			'in_page_activated' => false,
			'log_activated' => null,
			'excluded_categories' => ['category3'],
			'excluded_categories_activated' => null,
			'specific_features' => [],
			'country_restriction' => [],
			'is_multisite' => false,
			'custom_widget_css' => null,
		];

		$cmsFeatures = new CmsFeatures($data);
		$properties = $cmsFeatures->getProperties();

		$this->assertArrayHasKey('alma_enabled', $properties);
		$this->assertArrayNotHasKey('widget_cart_activated', $properties); // Should be filtered out (empty string)
		$this->assertArrayNotHasKey('widget_product_activated', $properties); // Should be filtered out (null)
		$this->assertArrayHasKey('used_fee_plans', $properties);
		$this->assertArrayNotHasKey('payment_method_position', $properties); // Should be filtered out (null)
		$this->assertArrayHasKey('in_page_activated', $properties);
		$this->assertArrayHasKey('excluded_categories', $properties);
		$this->assertArrayNotHasKey('excluded_categories_activated', $properties); // Should be filtered out (null)
		$this->assertArrayHasKey('specific_features', $properties);
		$this->assertArrayHasKey('country_restriction', $properties);
		$this->assertArrayHasKey('is_multisite', $properties);
		$this->assertArrayNotHasKey('custom_widget_css', $properties); // Should be filtered out (null)
	}
}