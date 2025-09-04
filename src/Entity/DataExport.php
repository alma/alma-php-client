<?php
/**
 * Copyright (c) 2018 Alma / Nabla SAS.
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
 */

namespace Alma\API\Entity;

use Alma\API\Exception\ParametersException;

class DataExport extends AbstractEntity
{
    /** @var bool */
    protected $complete;

    /** @var int Timestamp */
    protected $created;

    /** @var int Timestamp */
    protected $end;

    /** @var string */
    protected $id;

    /** @var bool */
    protected $includeChildAccounts;

    /** @var string */
    protected $merchant;

    /** @var int Timestamp */
    protected $start;

    /** @var string */
    protected $type;

    /** @var string Timestamp */
    protected $updated;

    /** @var string */
    protected $csvUrl;

    /** @var string */
    protected $pdfUrl;

    /** @var string */
    protected $xlsxUrl;

    /** @var string */
    protected $xmlUrl;

    /** @var string */
    protected $zipUrl;

    protected array $requiredFields = [
    ];

    protected array $optionalFields = [
        'complete'             => 'complete',
        'created'              => 'created',
        'end'                  => 'end',
        'id'                   => 'id',
        'includeChildAccounts' => 'include_child_accounts',
        'merchant'             => 'merchant',
        'start'                => 'start',
        'type'                 => 'type',
        'updated'              => 'updated',
        'csvUrl'               => 'csv_url',
        'pdfUrl'               => 'pdf_url',
        'xlsxUrl'              => 'xlsx_url',
        'xmlUrl'               => 'xml_url',
        'zipUrl'               => 'zip_url',
    ];

    public function getId(): string
    {
        return $this->id;
    }
}
