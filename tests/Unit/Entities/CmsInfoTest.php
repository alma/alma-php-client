<?php

namespace Unit\Entities;

use Alma\API\Entities\MerchantData\CmsInfo;
use PHPUnit\Framework\TestCase;

class CmsInfoTest extends TestCase
{
    const SDK_VERSION = '2.0.0';
    const SDK_NAME = 'Alma SDK';

    public function testConstructorSetsValuesCorrectly()
    {
        $data = [
            'cms_name' => 'WordPress',
            'cms_version' => '5.8',
            'third_parties_plugins' => ['plugin1', 'plugin2'],
            'theme_name' => 'theme1',
            'theme_version' => '1.1.0',
            'language_name' => 'PHP',
            'language_version' => '7.4',
            'alma_plugin_version' => '1.0.0',
            'alma_sdk_version' => self::SDK_VERSION,
            'alma_sdk_name' => self::SDK_NAME
        ];

        $cmsInfo = new CmsInfo($data);

        $this->assertEquals('WordPress', $cmsInfo->getProperties()['cms_name']);
        $this->assertEquals('5.8', $cmsInfo->getProperties()['cms_version']);
        $this->assertEquals(['plugin1', 'plugin2'], $cmsInfo->getProperties()['third_parties_plugins']);
        $this->assertEquals('theme1', $cmsInfo->getProperties()['theme_name']);
        $this->assertEquals('1.1.0', $cmsInfo->getProperties()['theme_version']);
        $this->assertEquals('PHP', $cmsInfo->getProperties()['language_name']);
        $this->assertEquals('7.4', $cmsInfo->getProperties()['language_version']);
        $this->assertEquals('1.0.0', $cmsInfo->getProperties()['alma_plugin_version']);
        $this->assertEquals(self::SDK_VERSION, $cmsInfo->getProperties()['alma_sdk_version']);
        $this->assertEquals(self::SDK_NAME, $cmsInfo->getProperties()['alma_sdk_name']);
    }

    public function testConstructorHandlesNullValuesCorrectly()
    {
        $data = [
            'cms_name' => null,
            'cms_version' => null,
            'third_parties_plugins' => null,
            'theme_name' => null,
            'theme_version' => null,
            'language_name' => null,
            'language_version' => null,
            'alma_plugin_version' => null,
            'alma_sdk_version' => null,
            'alma_sdk_name' => null
        ];

        $cmsInfo = new CmsInfo($data);

        $this->assertArrayNotHasKey('cms_name', $cmsInfo->getProperties());
        $this->assertArrayNotHasKey('cms_version', $cmsInfo->getProperties());
        $this->assertArrayNotHasKey('language_name', $cmsInfo->getProperties());
        $this->assertArrayNotHasKey('language_version', $cmsInfo->getProperties());
        $this->assertArrayNotHasKey('alma_plugin_version', $cmsInfo->getProperties());
        $this->assertArrayNotHasKey('alma_sdk_version', $cmsInfo->getProperties());
        $this->assertArrayNotHasKey('alma_sdk_name', $cmsInfo->getProperties());
        $this->assertArrayNotHasKey('third_parties_plugins', $cmsInfo->getProperties());
        $this->assertArrayNotHasKey('theme_name', $cmsInfo->getProperties());
        $this->assertArrayNotHasKey('theme_version', $cmsInfo->getProperties());
    }

    public function testGetPropertiesFiltersOutNullAndEmptyValues()
    {
        $data = [
            'cms_name' => 'WordPress',
            'cms_version' => '',
            'third_parties_plugins' => ['plugin1'],
            'theme_name' => '',
            'theme_version' => '',
            'language_name' => null,
            'language_version' => '',
            'alma_plugin_version' => null,
            'alma_sdk_version' => self::SDK_VERSION,
            'alma_sdk_name' => self::SDK_NAME
        ];

        $cmsInfo = new CmsInfo($data);
        $properties = $cmsInfo->getProperties();

        $this->assertArrayHasKey('cms_name', $properties);
        $this->assertArrayNotHasKey('cms_version', $properties); // Should be filtered out (empty string)
        $this->assertArrayHasKey('third_parties_plugins', $properties);
        $this->assertArrayNotHasKey('language_name', $properties); // Should be filtered out (null)
        $this->assertArrayNotHasKey('language_version', $properties); // Should be filtered out (empty string)
        $this->assertArrayHasKey('alma_sdk_version', $properties);
        $this->assertArrayHasKey('alma_sdk_name', $properties);
        $this->assertArrayNotHasKey('theme_name', $properties); // Should be filtered out (empty string)
        $this->assertArrayNotHasKey('theme_version', $properties); // Should be filtered out (empty string)
    }

    public function testGetPropertiesFiltersOutWithEmptyData()
    {
        $data = [];

        $cmsInfo = new CmsInfo($data);
        $properties = $cmsInfo->getProperties();

        $this->assertArrayNotHasKey('cms_name', $properties);
        $this->assertArrayNotHasKey('cms_version', $properties); // Should be filtered out (empty string)
        $this->assertArrayNotHasKey('third_parties_plugins', $properties);
        $this->assertArrayNotHasKey('theme_name', $properties);
        $this->assertArrayNotHasKey('theme_version', $properties);
        $this->assertArrayNotHasKey('language_name', $properties); // Should be filtered out (null)
        $this->assertArrayNotHasKey('language_version', $properties); // Should be filtered out (empty string)
        $this->assertArrayNotHasKey('alma_sdk_version', $properties);
        $this->assertArrayNotHasKey('alma_sdk_name', $properties);
    }
}