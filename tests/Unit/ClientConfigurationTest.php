<?php

namespace Alma\API\Tests\Unit;

use Alma\API\ClientConfiguration;
use Alma\API\Exceptions\ClientConfigurationException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ClientConfigurationTest extends TestCase
{
    /** @var ClientConfiguration */
    private ClientConfiguration $clientConfig;
    
    public function setUp(): void
    {
        parent::setUp();
        $this->clientConfig = new ClientConfiguration('sk_test_xxxxxxxxxxxx');
    }

    public function testTestMode()
    {
        $clientConfiguration = new ClientConfiguration('sk_test_xxxxxxxxxxxx', ClientConfiguration::TEST_MODE);
        $this->assertEquals(ClientConfiguration::TEST_MODE, $clientConfiguration->getMode());
    }

    /**
     * Ensure we can't create a ClientConfiguration with an invalid mode,
     * and it defaults to LIVE_MODE
     * @return void
     */
    public function testInvalidMode()
    {
        $clientConfiguration = new ClientConfiguration('sk_test_xxxxxxxxxxxx', 'INVALID_MODE');
        $this->assertEquals(ClientConfiguration::LIVE_MODE, $clientConfiguration->getMode());
    }

    /**
     * Ensure we can create a ClientConfiguration with a valid API key
     * Starting witch 'sk_test_' or 'sk_live_'
     */
    public function testConstructClientConfigurationException(): void
    {
        $this->expectException(ClientConfigurationException::class);
        new ClientConfiguration('invalid_key');
    }

    /**
     * Ensure we can create a ClientConfiguration with default values if not provided
     * @return array[]
     */
    public static function configDataProvider(): array
    {
        return [
            'valid-1' => [
                [
                    'base_uri' => 'https://api.getalma.eu',
                    'timeout' => 30,
                ],
                [
                    'base_uri' => 'https://api.getalma.eu',
                    'timeout' => 30,
                    'headers' => [
                        'Content-Type' => ['application/json']
                    ],
                    'verify' => true,
                    'retries' => 3,
                    'protocol_version' => '2.0',
                ]
            ]
        ];
    }

    /**
     * @dataProvider configDataProvider
     */
    public function testValidateConfiguration(array $config, array $expected)
    {
        $this->assertEquals($expected, $this->clientConfig->validateConfiguration($config));
    }

    public static function headersConfigDataProvider(): array
    {
        return [
            [
                [
                    'Content-Type' => ['application/json'],
                    'My-Own-Header' => ['my-value']
                ],
                [
                    'Content-Type' => ['application/json'],
                    'My-Own-Header' => ['my-value']
                ]
            ],
            [
                [
                    'My-Own-Header' => ['my-value']
                ],
                [
                    'Content-Type' => ['application/json'],
                    'My-Own-Header' => ['my-value']
                ]
            ],
            [
                [
                    'Content-Type' => ['application/json', 'application/xml']
                ],
                [
                    'Content-Type' => ['application/json', 'application/xml']
                ]
            ],
            [
                [
                    'My-Own-Header' => ['my-value'],
                    'My-Own-Header-2' => 'application/json', // Not an array
                    5 => ['application/json'], // Key is not a string
                    'Content-Type2' => 666, // Value is not an array
                    'Content-Type' => ['application/xml', 666], // Value is not a string
                ],
                [
                    'My-Own-Header' => ['my-value'],
                    'Content-Type' => ['application/xml']
                ]
            ]
        ];
    }

    /**
     * @dataProvider headersConfigDataProvider
     * @return void
     */
    public function testValidateHeaders(array $headers, array $expectedHeaders)
    {
        $headers = $this->clientConfig->validateHeaders($headers, ['Content-Type' => ['application/json']]);
        // Compare var_export of arrays to be strict in comparison
        $this->assertEquals($expectedHeaders, $headers);
    }

    /**
     * @return array
     */
    public static function baseUriConfigDataProvider(): array
    {
        return [
            [
                'https://api.getalma.eu',
                'https://api.getalma.eu'
            ]
        ];
    }

    /**
     * @return array
     */
    public static function timeoutConfigDataProvider(): array
    {
        return [
            [
                15,
                15
            ],
            [
                0,
                30
            ]
            ,
            [
                10000,
                30
            ]
        ];
    }
    /**
     * @dataProvider timeoutConfigDataProvider
     * @return void
     */
    public function testValidateTimeout($timeout, $expectedTimeout)
    {
        $timeout = $this->clientConfig->validateTimeout($timeout, 30);
        $this->assertEquals($expectedTimeout, $timeout);
    }

    /**
     * Ensure that the verify option is set to true by default
     */
    public function testValidateVerify()
    {
        $verify = $this->clientConfig->validateVerify(true, true);
        $this->assertTrue($verify);

        $verify = $this->clientConfig->validateVerify(false, true);
        $this->assertFalse($verify);

        $verify = $this->clientConfig->validateVerify(null, true);
        $this->assertTrue($verify);
    }

    public function testAddUserAgentComponent()
    {
        $this->clientConfig->addUserAgentComponent('alma-php-sdk', '1.0.0');
        $this->assertEquals('alma-php-sdk/1.0.0', $this->clientConfig->getUserAgentString());
    }

    /**
     * @return void
     */
    public function testValidateApiKey(): void
    {
        $this->assertEquals('sk_test_1234', $this->clientConfig->validateApiKey('sk_test_1234'));
        $this->assertEquals('sk_live_1234', $this->clientConfig->validateApiKey('sk_live_1234'));
    }

    /**
     * @return void
     */
    public function testValidateApiKeyInvalidArgumentException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid API key');
        $this->clientConfig->validateApiKey('invalid_key');
    }
}
