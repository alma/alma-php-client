<?php

namespace Alma\API\Tests\Unit;

use Alma\API\Lib\ClientOptionsValidator;
use Alma\API\Client;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Alma\API\ParamsError;

/**
 * Class ClientOptionsValidatorTest
 */
class ClientOptionsValidatorTest extends TestCase
{
    /** @var string  */
    const FAKE_API_URI = 'https://fake-api.getalma.eu';

    /**
     * Return options to test ClientOptionsValidator::validateOptions
     * @return array
     */
    public static function getClientOptions()
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
            ],
            [
                [
                    'user_agent_component' => [
                        'PrestaShop' => 2.3,
                        'Alma for PrestaShop' => 'v2.3',
                    ]
                ],
                [
                    'api_root' => [
                        Client::TEST_MODE => Client::SANDBOX_API_URL,
                        Client::LIVE_MODE => Client::LIVE_API_URL
                    ],
                    'force_tls' => 2,
                    'mode' => Client::LIVE_MODE,
                    'logger' => new NullLogger(),
                    'user_agent_component' => [
                        'PrestaShop' => 2.3,
                        'Alma for PrestaShop' => 'v2.3',
                    ]
                ]
            ]
        ];
    }

	/**
	 * Return faulty options to test ClientOptionsValidator::validateOptions
	 * @return array
	 */
	public static function getInvalidClientOptions()
	{
		return [
			'invalid api_root' => [
				[
					'api_root' => [
						'something wrong' => Client::SANDBOX_API_URL,
						'something wronger' => Client::LIVE_API_URL
					],
				],
			],
			'invalid tls' => [
				[
					'force_tls' => -1,
				],
			],
			'invalid mode' => [
				[
					'mode' => 'sad',
				],
			],
			'invalid user_agent_component' => [
				[
					'user_agent_component' => [
						'I dont give a key:value pair',
					]
				],
			],
		];
	}

    /**
     * @dataProvider getClientOptions
     * @return void
     * @throws ParamsError
     */
    public function testClientOptionsValidator($options, $expectedResult)
    {
        $validatedConfig = ClientOptionsValidator::validateOptions($options);

        $this->assertEquals($expectedResult, $validatedConfig);
    }

    /**
     * @dataProvider getInvalidClientOptions
     * @return void
     * @throws ParamsError
     */
    public function testFaultyClientOptionsValidator($options)
    {
        $this->expectException(ParamsError::class);
        ClientOptionsValidator::validateOptions($options);
    }
}
