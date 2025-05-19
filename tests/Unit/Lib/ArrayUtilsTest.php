<?php

namespace Alma\API\Tests\Unit\Lib;

use Alma\API\Exceptions\MissingKeyException;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Lib\ArrayUtils;
use Mockery\Adapter\Phpunit\MockeryTestCase;

class ArrayUtilsTest extends MockeryTestCase
{
    public static function arrayProvider(): array
    {
        return [
            'valid_assoc_array' => [['key' => 'value'], true],
            'empty_array' => [[], false],
            'indexed_array' => [[1, 2, 3], false],
            'mixed_keys_array' => [[0 => 'value1', 'key' => 'value2'], true],
            'non_array_input' => ['not_an_array', false],
        ];
    }

    /**
     * Ensure isAssocArray handles various scenarios
     * @dataProvider arrayProvider
     * @param mixed $input
     * @param bool $expected
     * @return void
     */
    public function testIsAssocArrayHandlesScenarios($input, bool $expected)
    {
        $result = ArrayUtils::isAssocArray($input);
        $this->assertSame($expected, $result);
    }

    public static function mandatoryKeysProvider(): array
    {
        return [
            'all_keys_present' => [['key1', 'key2'], ['key1' => 'value1', 'key2' => 'value2'], null],
            'missing_key' => [['key1', 'key3'], ['key1' => 'value1', 'key2' => 'value2'], MissingKeyException::class],
            'empty_keys_array' => [[], ['key1' => 'value1'], null],
            'empty_input_array' => [['key1'], [], MissingKeyException::class],
        ];
    }

    /**
     * Ensure checkMandatoryKeys handles various scenarios
     * @dataProvider mandatoryKeysProvider
     * @param array $keys
     * @param array $array
     * @param mixed $expectedException
     * @return void
     * @throws MissingKeyException
     */
    public function testCheckMandatoryKeysHandlesScenarios(array $keys, array $array, $expectedException)
    {
        if ($expectedException) {
            $this->expectException($expectedException);
        }

        (new ArrayUtils())->checkMandatoryKeys($keys, $array);

        if (!$expectedException) {
            $this->assertTrue(true); // No exception thrown
        }
    }

    public static function stringProvider(): array
    {
        return [
            'valid_string' => ['Hello World!', 'hello_world_'],
            'string_with_special_chars' => ['Hello@World#', 'hello_world_'],
            'string_with_spaces' => ['  Hello   World  ', '_hello_world_'],
            'empty_string' => ['', ParametersException::class],
            'string_with_only_special_chars' => ['@#$%^&*', ParametersException::class],
        ];
    }

    /**
     * Ensure slugify handles various scenarios
     * @dataProvider stringProvider
     * @param string $input
     * @param mixed $expected
     * @return void
     * @throws ParametersException
     */
    public function testSlugifyHandlesScenarios(string $input, $expected)
    {
        if ($expected === 'Alma\API\Exceptions\ParametersException') {
            $this->expectException($expected);
        }

        $result = (new ArrayUtils())->slugify($input);

        if ($expected !== 'Alma\API\Exceptions\ParametersException') {
            $this->assertSame($expected, $result);
        }
    }
}
