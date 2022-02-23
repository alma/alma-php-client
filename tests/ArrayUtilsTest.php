<?php

namespace Alma\API\Tests;

use Alma\API\ArrayUtils;
use Alma\API\Client;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use const Alma\API\LIVE_MODE;
use const Alma\API\TEST_MODE;

/**
 * Class ArrayUtils
 */
class ArrayUtilsTest extends TestCase
{
    /**
     * Return options to test ArrayUtils::almaArrayMergeRecursive
     * @return array
     */
    public function getTestOptions()
    {
        return [
            [
                [],
                [
                    'api_root' => [
                        Client::TEST_MODE => 'https://api.sandbox.getalma.eu',
                        Client::LIVE_MODE => 'https://api.getalma.eu'
                    ],
                    'force_tls' => 2,
                    'mode' => Client::LIVE_MODE,
                    'logger' => new NullLogger()
                ]
            ],
            [
                [
                    'api_root' => [
                        Client::TEST_MODE => 'https://fake-api.getalma.eu',
                        Client::LIVE_MODE => 'https://fake-api.getalma.eu'
                    ],
                    'force_tls' => 0,
                    'mode' => Client::TEST_MODE,
                    'logger' => new NullLogger()
                ],
                [
                    'api_root' => [
                        Client::TEST_MODE => 'https://fake-api.getalma.eu',
                        Client::LIVE_MODE => 'https://fake-api.getalma.eu'
                    ],
                    'force_tls' => 0,
                    'mode' => Client::TEST_MODE,
                    'logger' => new NullLogger()
                ]
            ],
            [
                [
                    'api_root' => Client::TEST_MODE,
                    'force_tls' => true,
                    'logger' => new NullLogger()
                ],
                [
                    'api_root' => Client::TEST_MODE,
                    'force_tls' => true,
                    'mode' => Client::LIVE_MODE,
                    'logger' => new NullLogger()
                ]
            ]
        ];
    }

    /**
     * @dataProvider getTestOptions
     * @return void
     */
    public function testAlmaArrayMergeRecursive($options, $expectedResult)
    {
        $defaultOptions = array(
            'api_root' => array(TEST_MODE => Client::SANDBOX_API_URL, LIVE_MODE => Client::LIVE_API_URL),
            'force_tls' => 2,
            'mode' => LIVE_MODE,
            'logger' => new NullLogger(),
        );
        $mergedOptions = ArrayUtils::almaArrayMergeRecursive($defaultOptions, $options);
        $this->assertEquals($expectedResult, $mergedOptions);
    }

    /**
     * Return options to test ArrayUtils::isAssocArray
     * @return array[]
     */
    public function getTestArrays()
    {
        return [
            [['a', 'b', 'c'], false],
            [["0" => 'a', "1" => 'b', "2" => 'c'], false],
            [["1" => 'a', "0" => 'b', "2" => 'c'], false],
            [["a" => 'a', "b" => 'b', "c" => 'c'], true],
        ];
    }

    /**
     * @dataProvider getTestArrays
     * @return void
     */
    public function testIsAssocArray($testArray, $expectedResult)
    {
        $result = ArrayUtils::isAssocArray($testArray);
        $this->assertEquals($expectedResult, $result);
    }
}
