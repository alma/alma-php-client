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
			'used_fee_plans' => ['Plan A'],
			'payment_method_position' => 1,
			'in_page_activated' => true,
			'log_activated' => false,
			'excluded_categories' => ['category1', 'category2'],
			'specific_features' => ['feature1', 'feature2'],
			'country_restriction' => ['FR', 'US'],
			'is_multisite' => false,
			'custom_widget_css' => true,
		];

		$cmsFeatures = new CmsFeatures($data);

		$this->assertTrue($cmsFeatures->getProperties()['alma_enabled']);
		$this->assertTrue($cmsFeatures->getProperties()['widget_cart_activated']);
		$this->assertFalse($cmsFeatures->getProperties()['widget_product_activated']);
		$this->assertEquals(['Plan A'], $cmsFeatures->getProperties()['used_fee_plans']);
		$this->assertEquals(1, $cmsFeatures->getProperties()['payment_method_position']);
		$this->assertTrue($cmsFeatures->getProperties()['in_page_activated']);
		$this->assertFalse($cmsFeatures->getProperties()['log_activated']);
		$this->assertEquals(['category1', 'category2'], $cmsFeatures->getProperties()['excluded_categories']);
		$this->assertEquals(['feature1', 'feature2'], $cmsFeatures->getProperties()['specific_features']);
		$this->assertEquals(['FR', 'US'], $cmsFeatures->getProperties()['country_restriction']);
		$this->assertFalse($cmsFeatures->getProperties()['is_multisite']);
		$this->assertTrue($cmsFeatures->getProperties()['custom_widget_css']);
	}

	public function testConstructorHandlesNullValuesCorrectly()
	{
		$data = [
			'alma_enabled' => null,
			'widget_cart_activated' => null,
			'widget_product_activated' => null,
			'used_fee_plans' => null,
			'payment_method_position' => null,
			'in_page_activated' => null,
			'log_activated' => null,
			'excluded_categories' => null,
			'specific_features' => null,
			'country_restriction' => null,
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
        $this->assertArrayNotHasKey('excluded_categories', $properties);
        $this->assertArrayNotHasKey('specific_features', $properties);
        $this->assertArrayNotHasKey('country_restriction', $properties);
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
		$this->assertArrayHasKey('specific_features', $properties);
		$this->assertArrayHasKey('country_restriction', $properties);
		$this->assertArrayHasKey('is_multisite', $properties);
		$this->assertArrayNotHasKey('custom_widget_css', $properties); // Should be filtered out (null)
	}

    public function testGetPropertiesFiltersOutWithEmptyData()
    {
        $data = [];

        $cmsFeatures = new CmsFeatures($data);
        $properties = $cmsFeatures->getProperties();

        $this->assertArrayNotHasKey('alma_enabled', $properties);
        $this->assertArrayNotHasKey('widget_cart_activated', $properties);
        $this->assertArrayNotHasKey('widget_product_activated', $properties);
        $this->assertArrayNotHasKey('used_fee_plans', $properties);
        $this->assertArrayNotHasKey('payment_method_position', $properties);
        $this->assertArrayNotHasKey('in_page_activated', $properties);
        $this->assertArrayNotHasKey('excluded_categories', $properties);
        $this->assertArrayNotHasKey('specific_features', $properties);
        $this->assertArrayNotHasKey('country_restriction', $properties);
        $this->assertArrayNotHasKey('is_multisite', $properties);
        $this->assertArrayNotHasKey('custom_widget_css', $properties);
    }
}