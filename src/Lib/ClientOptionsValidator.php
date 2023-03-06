<?php
/**
 * Copyright (c) 2018 Alma / Nabla SAS
 *
 * THE MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and
 * to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @author    Alma / Nabla SAS <contact@getalma.eu>
 * @copyright Copyright (c) 2018 Alma / Nabla SAS
 * @license   https://opensource.org/licenses/MIT The MIT License
 *
 */

namespace Alma\API\Lib;

use Alma\API\Client;
use Alma\API\ParamsError;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ClientOptionsValidator
{
    /**
     * Alma client config validation.
     *
     * @param $options
     *              - api_root              string|array[$mode => string]
     *                                            API root URL to use. If you need different URLs for the
     *                                            test and live modes, provide an array with the keys
     *                                            'test' and 'live' and the URLs to use as values.
     *                                            Default: "https://api.getalma.eu"
     *
     *              - force_tls             int|boolean 0, 1 or 2 will force TLS 1.0, 1.1 or 1.2 when connecting to the API.
     *                                          `false` will not try to force TLS; `true` wil fallback to default value.
     *                                          If set to 0/1/2/true, TLS will be forced even if the API ROOT uses the
     *                                          "http://" scheme.
     *                                          Default: 2
     *              - mode                  string  'test' or 'live'. Default: 'live'
     *              - logger                Psr\Log\LoggerInterface The logger instance to use for errors/warnings
     *              - user_agent_component  array[user_agent => version] user_agent and version should be string
     *
     * @throws ParamsError
     */
    public static function validateOptions(array $options = array())
    {
        $config = [
            'api_root' => [
                Client::TEST_MODE => isset($_ENV['ALMA_TEST_API_ROOT']) ? $_ENV['ALMA_TEST_API_ROOT'] : Client::SANDBOX_API_URL,
                Client::LIVE_MODE => isset($_ENV['ALMA_LIVE_API_ROOT']) ? $_ENV['ALMA_LIVE_API_ROOT'] : Client::LIVE_API_URL
            ],
            'force_tls' => 2,
            'mode' => Client::LIVE_MODE,
            'logger' => new NullLogger(),
        ];

        if (isset($options['api_root'])) {
            $config['api_root'] = self::validateApiRootOption($options['api_root']);
        }

        if (isset($options['force_tls'])) {
            $config['force_tls'] = self::validateForceTLSOption($options['force_tls']);
        }

        if (isset($options['mode'])) {
            $config['mode'] = self::validateModeOption($options['mode']);
        }

        if (isset($options['logger'])) {
            $config['logger'] = self::validateLoggerOption($options['logger']);
        }

        if (isset($options['user_agent_component'])) {
            $config['user_agent_component'] = self::validateUserAgentComponentOption($options['user_agent_component']);
        }
        return $config;
    }

    /**
     * @param string|string[] $api_root
     *
     * @return string[]
     * @throws ParamsError
     */
    public static function validateApiRootOption($api_root)
    {
        if (is_string($api_root)) {
            return [
                Client::TEST_MODE => $api_root,
                Client::LIVE_MODE => $api_root
            ];
        }
        if (is_array($api_root) && isset($api_root[Client::TEST_MODE]) && isset($api_root[Client::LIVE_MODE])) {
            return [
                Client::TEST_MODE => $api_root[Client::TEST_MODE],
                Client::LIVE_MODE => $api_root[Client::LIVE_MODE]
            ];
        }
        throw new ParamsError('option \'api_root\' is not configured properly');
    }

    /**
     * @param bool|int $force_tls
     *
     * @return bool|int
     * @throws ParamsError
     */
    public static function validateForceTLSOption($force_tls)
    {
        if ($force_tls === true || $force_tls === 2) {
            return 2;
        }
        if (in_array($force_tls, [0, 1, false])) {
            return $force_tls;
        }
        throw new ParamsError('option \'force_tls\' is not configured properly');
    }

    /**
     * @param string $mode
     *
     * @return string
     * @throws ParamsError
     */
    public static function validateModeOption($mode)
    {
        if (in_array($mode, [Client::TEST_MODE, Client::LIVE_MODE])) {
            return $mode;
        }
        throw new ParamsError('option \'mode\' is not configured properly');
    }

    /**
     * @param LoggerInterface $logger
     *
     * @return LoggerInterface
     * @throws ParamsError
     */
    public static function validateLoggerOption($logger)
    {
        if ($logger instanceof LoggerInterface) {
            return $logger;
        }
        throw new ParamsError('option \'logger\' is not configured properly');
    }

    /**
     * @param array $user_agent_components
     *
     * @return array
     * @throws ParamsError
     */
    public static function validateUserAgentComponentOption(array $userAgentComponents)
    {
        if (is_array($userAgentComponents) && count($userAgentComponents) > 1) {
            return $userAgentComponents;
        }
        throw new ParamsError('option \'user_agent_component\' is not configured properly');
    }
}
