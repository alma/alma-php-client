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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class ClientContext
{
    /** @var string */
    public $apiKey;

    /** @var LoggerInterface  */
    public $logger;

    /** @var array */
    private $options;

    /** @var array */
    private $userAgentComponents;

    public function __construct($apiKey, $options)
    {
        $this->apiKey = $apiKey;
        $this->options = $options;
        $this->setLogger($options['logger']);

        $this->userAgentComponents = array();

        if (isset($options['user_agent_component'])) {
            foreach ($options['user_agent_component'] as $component => $version) {
                $this->addUserAgentComponent($component, $version);
            }
        }
    }

    /**
     * Returns the full API endpoint URL for the given path, depending on the current mode (live or test)
     *
     * @param $path
     * @return string
     */
    public function urlFor($path)
    {
        $root = $this->options['api_root'][$this->options['mode']];
        return rtrim($root, '/') . '/' . ltrim($path, '/');
    }

    /**
     * @return int|false    Either not to force TLS (false), or the TLS subversion to force (i.e. x for TLS 1.x)
     */
    public function forcedTLSVersion()
    {
        return $this->options['force_tls'];
    }

    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        if (!$logger) {
            $this->logger = new NullLogger();
        } else {
            $this->logger = $logger;
        }
    }

    public function addUserAgentComponent($component, $version)
    {
        $this->userAgentComponents[] = "$component/$version";
    }

    public function getUserAgentString()
    {
        return implode("; ", array_reverse($this->userAgentComponents));
    }
}
