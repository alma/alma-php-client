<?php

namespace Alma\API\Tests\Unit\Infrastructure;

use Alma\API\Domain\ValueObject\Environment;
use Alma\API\Infrastructure\Exception\ParametersException;
use Alma\API\Infrastructure\Helper\ClientConfigurationValidatorHelper;
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
                        Environment::TEST_MODE => Environment::SANDBOX_API_URL,
                        Environment::LIVE_MODE => Environment::LIVE_API_URL
                    ],
                    'force_tls' => 2,
                    'mode' => Environment::LIVE_MODE,
                    'logger' => new NullLogger()
                ]
            ],
            [
                [
                    'api_root' => [
                        Environment::TEST_MODE => self::FAKE_API_URI,
                        Environment::LIVE_MODE => self::FAKE_API_URI
                    ],
                    'force_tls' => 0,
                    'mode' => Environment::TEST_MODE,
                    'logger' => new NullLogger()
                ],
                [
                    'api_root' => [
                        Environment::TEST_MODE => self::FAKE_API_URI,
                        Environment::LIVE_MODE => self::FAKE_API_URI
                    ],
                    'force_tls' => 0,
                    'mode' => Environment::TEST_MODE,
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
                        Environment::TEST_MODE => self::FAKE_API_URI,
                        Environment::LIVE_MODE => self::FAKE_API_URI
                    ],
                    'force_tls' => 2,
                    'mode' => Environment::LIVE_MODE,
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
                        Environment::TEST_MODE => Environment::SANDBOX_API_URL,
                        Environment::LIVE_MODE => Environment::LIVE_API_URL
                    ],
                    'force_tls' => 2,
                    'mode' => Environment::LIVE_MODE,
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
						'something wrong' => Environment::SANDBOX_API_URL,
						'something wronger' => Environment::LIVE_API_URL
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
        $validatedConfig = ClientConfigurationValidatorHelper::validateOptions($options);

        $this->assertEquals($expectedResult, $validatedConfig);
    }

    /**
     * @dataProvider getInvalidClientOptions
     * @return void
     */
    public function testFaultyClientOptionsValidator($options)
    {
        $this->expectException(ParametersException::class);

        ClientConfigurationValidatorHelper::validateOptions($options);
    }
}
