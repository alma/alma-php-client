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

namespace Alma\API\Infrastructure\Endpoint;

use Alma\API\Domain\Entity\DataExport;
use Alma\API\Infrastructure\Exception\Endpoint\DataExportEndpointException;
use Alma\API\Infrastructure\Exception\ParametersException;
use Alma\API\Infrastructure\Exception\RequestException;
use Psr\Http\Client\ClientExceptionInterface;

class DataExportEndpoint extends AbstractEndpoint
{
    const DATA_EXPORTS_ENDPOINT = '/v1/data-exports';
    const ACCEPTED_FORMAT = ['csv', 'xlsx'];

    /**
     * @param string $type Type of data export
     * @param array $data Additional data
     *
     * @return DataExport
     *
     * @throws DataExportEndpointException
     */
    public function create(string $type, array $data = []): DataExport
    {
        $data['type'] = $type;
        try {
            $request = null;
            $request = $this->createPostRequest(self::DATA_EXPORTS_ENDPOINT, $data);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new DataExportEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new DataExportEndpointException($response->getReasonPhrase(), $request, $response);
        }

        $json = $response->getJson();
        try {
            $dataExport = new DataExport($json);
        } catch (ParametersException $e) {
            throw new DataExportEndpointException($e->getMessage(), $request);
        }

        return $dataExport;
    }

    /**
     * @param string $reportId
     *
     * @return DataExport
     *
     * @throws DataExportEndpointException
     */
    public function fetch(string $reportId): DataExport
    {
        try {
            $request = null;
            $request = $this->createGetRequest(self::DATA_EXPORTS_ENDPOINT . '/' . $reportId);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new DataExportEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new DataExportEndpointException($response->getReasonPhrase(), $request, $response);
        }

        $json = $response->getJson();
        try {
            $dataExport = new DataExport($json);
        } catch (ParametersException $e) {
            throw new DataExportEndpointException($e->getMessage(), $request);
        }
        return $dataExport;
    }

    /**
     * @param string $reportId
     *
     * @param string $format only csv or xlsx
     *
     * @return mixed
     *
     * @throws DataExportEndpointException
     * @throws ParametersException
     */
    public function download(string $reportId, string $format)
    {
        if (!in_array($format, self::ACCEPTED_FORMAT)) {
            throw new ParametersException("Invalid format: $format. Accepted format are: " . implode(', ', self::ACCEPTED_FORMAT));
        }

        try {
            $request = null;
            $request = $this->createGetRequest(self::DATA_EXPORTS_ENDPOINT . sprintf('/%s', $reportId), ['format' => $format]);
            $response = $this->client->sendRequest($request);
        } catch (ClientExceptionInterface|RequestException $e) {
            throw new DataExportEndpointException($e->getMessage(), $request);
        }

        if ($response->isError()) {
            throw new DataExportEndpointException($response->getReasonPhrase(), $request, $response);
        }

        return $response->getFile();
    }
}
