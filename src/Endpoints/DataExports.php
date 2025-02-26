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

namespace Alma\API\Endpoints;

use Alma\API\Entities\DataExport;
use Alma\API\Exceptions\ParametersException;
use Alma\API\Exceptions\RequestException;
use Alma\API\ParamsError;
use Alma\API\RequestError;

class DataExports extends Base
{
    const DATA_EXPORTS_PATH = '/v1/data-exports';
    const ACCEPTED_FORMAT = ['csv', 'xlsx'];

    /**
     * @param $data
     *
     * @return DataExport
     *
     * @throws RequestException|RequestError
     */
    public function create($data)
    {
        $res = $this->request(self::DATA_EXPORTS_PATH)->setRequestBody($data)->post();

        if ($res->isError()) {
            throw new RequestException($res->errorMessage, null, $res);
        }

        return new DataExport($res->json);
    }

    /**
     * @param $reportId
     *
     * @return DataExport
     *
     * @throws RequestException|RequestError
     */
    public function fetch($reportId)
    {
        $res = $this->request(self::DATA_EXPORTS_PATH . '/' . $reportId)->get();

        if ($res->isError()) {
            throw new RequestException($res->errorMessage, null, $res);
        }

        return new DataExport($res->json);
    }

    /**
     * @param $reportId
     *
     * @param string $format only csv or xlsx
     *
     * @return mixed
     *
     * @throws RequestException|RequestError|ParametersException
     */
    public function download($reportId, $format)
    {
        if (!in_array($format, self::ACCEPTED_FORMAT)) {
            throw new ParametersException("Invalid format: $format. Accepted format are: " . implode(', ', self::ACCEPTED_FORMAT));
        }

        $res = $this->request(self::DATA_EXPORTS_PATH . '/' . $reportId)
            ->setQueryParams(['format' => $format])
            ->get();

        if ($res->isError()) {
            throw new RequestException($res->errorMessage, null, $res);
        }

        return $res->data;
    }
}
