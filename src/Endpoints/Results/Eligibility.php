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

namespace Alma\API\Endpoints\Results;

use Alma\API\Response;

class Eligibility
{
    public $isEligible;
    public $reasons;
    public $constraints;

    /**
     * Eligibility constructor.
     * @param Response $res
     */
    public function __construct($res)
    {
        // Supporting some legacy behaviour where the eligibility check would return a 406 error if not eligible,
        // instead of 200 OK + {"eligible": false}
        if (array_key_exists("eligible", $res->json)) {
            $this->isEligible = $res->json["eligible"];
        } else {
            $this->isEligible = ($res->responseCode == 200);
        }

        if (array_key_exists("reasons", $res->json)) {
            $this->reasons = $res->json["reasons"];
        }

        if (array_key_exists("constraints", $res->json)) {
            $this->constraints = $res->json["constraints"];
        }
    }
}
