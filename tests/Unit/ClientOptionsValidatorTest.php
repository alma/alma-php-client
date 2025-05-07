<?php

namespace Alma\API\Tests\Unit;

use Alma\API\Configuration;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Lib\ClientOptionsValidator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Psr\Log\NullLogger;

/**
 * Class ClientOptionsValidatorTest
 */
class ClientOptionsValidatorTest extends MockeryTestCase
{
    /** @var string  */
    const FAKE_API_URI = 'https://fake-api.getalma.eu';

    /**
     * Return options to test ClientOptionsValidator::validateOptions
     * @return array
     */
    public static function getClientOptions(): array
    {
        return [
            [
                [],
                [
                    'api_root' => [
                        Configuration::TEST_MODE => Configuration::SANDBOX_API_URL,
                        Configuration::LIVE_MODE => Configuration::LIVE_API_URL
                    ],
                    'force_tls' => 2,
                    'mode' => Configuration::LIVE_MODE,
                    'logger' => new NullLogger()
                ]
            ],
            [
                [
                    'api_root' => [
                        Configuration::TEST_MODE => self::FAKE_API_URI,
                        Configuration::LIVE_MODE => self::FAKE_API_URI
                    ],
                    'force_tls' => 0,
                    'mode' => Configuration::TEST_MODE,
                    'logger' => new NullLogger()
                ],
                [
                    'api_root' => [
                        Configuration::TEST_MODE => self::FAKE_API_URI,
                        Configuration::LIVE_MODE => self::FAKE_API_URI
                    ],
                    'force_tls' => 0,
                    'mode' => Configuration::TEST_MODE,
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
                        Configuration::TEST_MODE => self::FAKE_API_URI,
                        Configuration::LIVE_MODE => self::FAKE_API_URI
                    ],
                    'force_tls' => 2,
                    'mode' => Configuration::LIVE_MODE,
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
                        Configuration::TEST_MODE => Configuration::SANDBOX_API_URL,
                        Configuration::LIVE_MODE => Configuration::LIVE_API_URL
                    ],
                    'force_tls' => 2,
                    'mode' => Configuration::LIVE_MODE,
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
	public static function getInvalidClientOptions(): array
    {
		return [
			'invalid api_root' => [
				[
					'api_root' => [
						'something wrong' => Configuration::SANDBOX_API_URL,
						'something wronger' => Configuration::LIVE_API_URL
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
     * @throws ParametersException
     */
    public function testClientOptionsValidator($options, $expectedResult)
    {
        /** @var mixed $validatedConfig */
        $validatedConfig = ClientOptionsValidator::validateOptions($options);

        $this->assertEquals($expectedResult, $validatedConfig);
    }

    /**
     * @dataProvider getInvalidClientOptions
     * @return void
     */
    public function testFaultyClientOptionsValidator($options)
    {
        $this->expectException(ParametersException::class);

        ClientOptionsValidator::validateOptions($options);
    }
}
