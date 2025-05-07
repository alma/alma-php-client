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

namespace Alma\API;

class Configuration
{
    const LIVE_MODE = 'live';
    const TEST_MODE = 'test';

    const LIVE_API_URL = 'https://api.getalma.eu';
    const SANDBOX_API_URL = 'https://api.sandbox.getalma.eu';

    private array $userAgentComponents;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'base_uri' => '',
            'timeout'  => 30,
            'headers'  => [],
            'auth'     => null,
            'verify'   => true,
            'retries'  => 0,
        ], $config);
    }

    public function all(): array
    {
        return $this->config;
    }

    public function getApiKey(): ?string
    {
        return $this->config['auth']['api_key'] ?? null;
    }

    public function getBaseUri(): string
    {
        return $this->config['base_uri'] ?? '';
    }

    public function getHeaders(): array
    {
        return $this->config['headers'] ?? [];
    }

    public function getTimeout(): int
    {
        return $this->config['timeout'] ?? 30;
    }

    public function getSslVerify(): bool
    {
        return $this->config['verify'] ?? true;
    }

    public function addUserAgentComponent($component, $version)
    {
        $this->userAgentComponents[] = "$component/$version";
    }

    public function getUserAgentString(): string
    {
        return implode("; ", array_reverse($this->userAgentComponents));
    }
}
