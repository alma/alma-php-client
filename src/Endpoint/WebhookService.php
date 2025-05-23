<?php
/**
 * 2018-2019 Alma SAS
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
 * @author    Alma SAS <contact@getalma.eu>
 * @copyright 2018-2019 Alma SAS
 * @license   https://opensource.org/licenses/MIT The MIT License
 */


namespace Alma\API\Endpoint;

use Alma\API\Entities\Webhook;
use Alma\API\Exceptions\RequestException;
use Alma\API\Exceptions\WebhookServiceException;
use Psr\Http\Client\ClientExceptionInterface;

class WebhookService extends AbstractService
{
    const WEBHOOKS_ENDPOINT = '/v1/webhooks';

    /**
     * @param string $type The webhook type, see available constants in Alma\API\Entities\Webhook
     * @param string $url The URL to be called for that webhook
     *
     * @return Webhook
     * @throws WebhookServiceException
     */
    public function create(string $type, string $url): Webhook
    {
        $data = array("type" => $type, "url" => $url);

        try {
            $request = null;
            $request = $this->createPostRequest(self::WEBHOOKS_ENDPOINT, $data);
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new WebhookServiceException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new WebhookServiceException($response->getReasonPhrase(), $request, $response);
        }

        return new Webhook($response->getJson());
    }

    /**
     * @param string $id The external ID for the payment to fetch
     *
     * @return Webhook
     * @throws WebhookServiceException
     */
    public function fetch(string $id): Webhook
    {
        try {
            $request = null;
            $request = $this->createGetRequest(self::WEBHOOKS_ENDPOINT . sprintf("/%s", $id));
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new WebhookServiceException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new WebhookServiceException($response->getReasonPhrase(), $request, $response);
        }

        return new Webhook($response->getJson());
    }

    /**
     * Delete a webhook
     *
     * @param string $id The ID of the Webhook to delete
     *
     * @return true True if the deletion call worked (throws otherwise)
     * @throws WebhookServiceException
     */
    public function delete(string $id): bool
    {
        try {
            $request = null;
            $request = $this->createDeleteRequest(self::WEBHOOKS_ENDPOINT . sprintf("/%s", $id));
            $response = $this->client->sendRequest($request);
        } catch (RequestException|ClientExceptionInterface $e) {
            throw new WebhookServiceException($e->getMessage(), $request);
        }
        if ($response->isError()) {
            throw new WebhookServiceException($response->getReasonPhrase(), $request, $response);
        }

        return true;
    }
}
