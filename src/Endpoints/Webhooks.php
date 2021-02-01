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


namespace Alma\API\Endpoints;

use Alma\API\Entities\Webhook;
use Alma\API\RequestError;

class Webhooks extends Base
{
    const WEBHOOKS_PATH = '/v1/webhooks';

    /**
     * @param string $type  The webhook type, see available constants in Alma\API\Entities\Webhook
     * @param string $url   The URL to be called for that webhook
     *
     * @return Webhook
     * @throws RequestError
     */
    public function create($type, $url)
    {
        $data = array("type" => $type, "url" => $url);
        $res = $this->request(self::WEBHOOKS_PATH)->setRequestBody($data)->post();

        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }

        return new Webhook($res->json);
    }

    /**
     * @param string $id The external ID for the payment to fetch
     *
     * @return Webhook
     * @throws RequestError
     */
    public function fetch($id)
    {
        $res = $this->request(self::WEBHOOKS_PATH . "/$id")->get();

        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }

        return new Webhook($res->json);
    }

    /**
     * Delete a webhook
     *
     * @param string $id The ID of the Webhook to delete
     *
     * @return true True if the deletion call worked (throws otherwise)
     * @throws RequestError
     */
    public function delete($id)
    {
        $res = $this->request(self::WEBHOOKS_PATH . "/$id")->delete();

        if ($res->isError()) {
            throw new RequestError($res->errorMessage, null, $res);
        }

        return true;
    }
}
