<?php

namespace Alma\API\Tests\Unit;

use Alma\API\ClientContext;
use PHPUnit\Framework\TestCase;
use Alma\API\Client;
use Psr\Log\NullLogger;

/**
 * Class ClientContextTest
 */
class ClientContextTest extends TestCase
{
    /** @var string  */
    const FAKE_API_URI = 'https://fake-api.getalma.eu';

    /**
     * Return faulty options to test ClientOptionsValidator::validateOptions
     * @return array
     */
    public static function getClientconfigOptions()
    {
        return [
            'empty' => [[
                'api_root' => [
                    Client::TEST_MODE => self::FAKE_API_URI,
                    Client::LIVE_MODE => self::FAKE_API_URI
                ],
                'force_tls' => 0,
                'mode' => Client::TEST_MODE,
                'logger' => new NullLogger()
            ]],
            'user_agent_component' => [[
                'api_root' => [
                    Client::TEST_MODE => self::FAKE_API_URI,
                    Client::LIVE_MODE => self::FAKE_API_URI
                ],
                'force_tls' => 0,
                'mode' => Client::TEST_MODE,
                'logger' => new NullLogger(),
                'user_agent_component' => ['Magento' => '12.3']
            ]],
        ];
    }

    /**
     * @dataProvider getClientconfigOptions
     * @return void
     * @throws ParamsError
     */
    public function testClientContext($options)
    {
        $context = new ClientContext("sk_fake_api_key", $options);

        $this->assertInstanceOf(ClientContext::class, $context);
    }
}
