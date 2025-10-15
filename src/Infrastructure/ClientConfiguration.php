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

namespace Alma\API\Infrastructure;

use Alma\API\Domain\ValueObject\Environment;
use Alma\API\Infrastructure\Exception\ClientConfigurationException;
use InvalidArgumentException;

class ClientConfiguration
{
    const LIVE_API_URL = 'https://api.getalma.eu';
    const SANDBOX_API_URL = 'https://api.sandbox.getalma.eu';

    private string $apiKey;

    private array $userAgentComponents = [];
    private array $config = [];
    private string $baseUri = self::LIVE_API_URL;

    private array $errors = [];
    private Environment $environment;

    /**
     * ClientConfiguration constructor.
     *
     * @param string $apiKey
     * @param ?Environment $environment
     * @param array $options
     */
    public function __construct(string $apiKey, ?Environment $environment = null, array $options = [])
    {
        try {
            if (is_null($environment)) {
                $environment = new Environment(Environment::LIVE_MODE);
            }
            $this->environment = $environment;

            // Check if the auth is valid
            $this->apiKey = $this->validateApiKey($apiKey);

            // Check if the config is valid
            $this->config = $this->validateConfiguration($options);


            // Define Base URI based on the mode
            if ($this->environment->isLiveMode()) {
                $this->baseUri = self::LIVE_API_URL;
            } elseif ($this->environment->isTestMode()) {
                $this->baseUri = self::SANDBOX_API_URL;
            } else {
                $this->addError("Invalid mode: $environment");
            }
            
        } catch (InvalidArgumentException $e) {
            $this->addError("Invalid configuration: " . $e->getMessage());
        }
    }

    /**
     * Validate the configuration options
     *
     * @param array $options
     * @return array
     * @throws InvalidArgumentException
     */
    public function validateConfiguration(array $options): array
    {
        $options["timeout"] = $this->validateTimeout($options["timeout"] ?? null);
        $options["headers"] = $this->validateHeaders($options["headers"] ?? []);
        $options["verify"] = $this->validateVerify($options["verify"] ?? null);
        $options["retries"] = $this->validateRetries($options["retries"] ?? null);
        $options["protocol_version"] = $this->validateProtocolVersion($options["protocol_version"] ?? null);

        return $options;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getBaseUri(): string
    {
        return $this->baseUri;
    }

    public function getHeaders(): array
    {
        return $this->config['headers'];
    }

    public function getTimeout(): int
    {
        return $this->config['timeout'];
    }

    public function getSslVerify(): bool
    {
        return $this->config['verify'];
    }

    public function getProtocolVersion(): string
    {
        return $this->config['protocol_version'];
    }

    public function addUserAgentComponent($component, $version)
    {
        $this->userAgentComponents[] = "$component/$version";
    }

    public function getUserAgentString(): string
    {
        return implode("; ", array_reverse($this->userAgentComponents));
    }

    /**
     * @param int|null $timeout
     * @param int $defaultTimeout
     * @return int
     */
    public function validateTimeout(?int $timeout, int $defaultTimeout = 30): int
    {
        if (is_null($timeout) || $timeout <= 0 || $timeout > 600) {
            $timeout = $defaultTimeout;
        }
        return $timeout;
    }

    /**
     * @param array $headers
     * @param array $defaultHeaders
     * @return array
     */
    public function validateHeaders(array $headers, array $defaultHeaders = ['Content-Type' => ['application/json']]): array
    {
        $filteredArray = [];
        foreach ($headers as $headerKey => $headerValues) {
            if (is_string($headerKey) && is_array($headerValues)) {
                $filteredArray[$headerKey] = array_filter($headerValues, function ($headerValue) {
                    return is_string($headerValue);
                });
            }
        }
        return array_merge($defaultHeaders, $filteredArray);
    }

    /**
     * @param string $apiKey
     * @return string
     */
    public function validateApiKey(string $apiKey): string
    {
        if (in_array(substr($apiKey, 0, 8), ['sk_live_', 'sk_test_'])) {
            return $apiKey;
        } else {
            $this->addError("Invalid API key");
            return '';
        }
    }

    /**
     * @param bool|null $verify
     * @param bool $defaultVerify
     * @return bool
     */
    public function validateVerify(?bool $verify, bool $defaultVerify = true): bool
    {
        if (is_null($verify)) {
            return $defaultVerify;
        }
        return $verify;
    }

    /**
     * @param int|null $retries
     * @param int $defaultRetries
     * @return int
     */
    public function validateRetries(?int $retries, int $defaultRetries = 3): int
    {
        if (is_null($retries) || $retries < 0 || $retries > 10) {
            $retries = $defaultRetries;
        }
        return $retries;
    }

    /**
     * @param string|null $version
     * @param string $defaultVersion
     * @return string
     */
    public function validateProtocolVersion(?string $version, string $defaultVersion = '2.0'): string
    {
        if (is_null($version) || !in_array($version, ['1.0', '1.1', '2.0'])) {
            $version = $defaultVersion;
        }
        return $version;
    }

    public function addError(string $message): void
    {
        $this->errors[$message] = $message;
    }

    public function hasError(): bool {
        return !empty($this->errors);
    }

    public function getErrors(): array {
        return array_values($this->errors);
    }
}
