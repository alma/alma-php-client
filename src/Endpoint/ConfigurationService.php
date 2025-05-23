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

namespace Alma\API\Endpoint;

use Alma\API\Exceptions\ConfigurationServiceException;
use Alma\API\Exceptions\RequestException;
use Psr\Http\Client\ClientExceptionInterface;

class ConfigurationService extends AbstractService
{
    const CONFIGURATION_API_ENDPOINT = '/v1/integration-configuration/api';

    /**
     * @param string $url The URL to send to Alma for integrations configuration
     * @throws ConfigurationServiceException
     */
    public function sendIntegrationsConfigurationsUrl(string $url)
    {
        try {
            $request = null;
            $request = $this->createPutRequest(self::CONFIGURATION_API_ENDPOINT, ["collect_data_url" => $url]);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new ConfigurationServiceException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new ConfigurationServiceException($response->getReasonPhrase(), $request, $response);
        }
    }
}
