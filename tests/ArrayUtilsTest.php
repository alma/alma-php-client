<?php

namespace Alma\API\Tests;

use Alma\API\Lib\ArrayUtils;
use Alma\API\Lib\ClientOptionsValidator;
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
    /** @var string  */
    const FAKE_API_URI = 'https://fake-api.getalma.eu';

    /**
     * Return options to test ArrayUtils::almaArrayMergeRecursive
     * @return array
     */
    public function getClientOptions()
    {
        return [
            [
                [],
                [
                    'api_root' => [
                        Client::TEST_MODE => Client::SANDBOX_API_URL,
                        Client::LIVE_MODE => Client::LIVE_API_URL
                    ],
                    'force_tls' => 2,
                    'mode' => Client::LIVE_MODE,
                    'logger' => new NullLogger()
                ]
            ],
            [
                [
                    'api_root' => [
                        Client::TEST_MODE => self::FAKE_API_URI,
                        Client::LIVE_MODE => self::FAKE_API_URI
                    ],
                    'force_tls' => 0,
                    'mode' => Client::TEST_MODE,
                    'logger' => new NullLogger()
                ],
                [
                    'api_root' => [
                        Client::TEST_MODE => self::FAKE_API_URI,
                        Client::LIVE_MODE => self::FAKE_API_URI
                    ],
                    'force_tls' => 0,
                    'mode' => Client::TEST_MODE,
                    'logger' => new NullLogger()
                ]
            ],
            [
                [
                    'api_root' => self::FAKE_API_URI,
                    'force_tls' => true,
                    'logger' => new NullLogger()
                ],
                [
                    'api_root' => [
                        Client::TEST_MODE => self::FAKE_API_URI,
                        Client::LIVE_MODE => self::FAKE_API_URI
                    ],
                    'force_tls' => 2,
                    'mode' => Client::LIVE_MODE,
                    'logger' => new NullLogger()
                ]
            ]
        ];
    }

    /**
     * @dataProvider getClientOptions
     * @return void
     */
    public function testClientOptionsValidator($options, $expectedResult)
    {
        $validatedConfig = ClientOptionsValidator::validateOptions($options);

        $this->assertEquals($expectedResult, $validatedConfig);
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
