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

namespace Alma\API\Domain\Entity;

/**
 * Class DataExport
 * @package Alma\API\Entity
 *
 * @link https://docs.almapay.com/reference/data-export
 */
class DataExport extends AbstractEntity
{
    /** @var bool Indicates whether the generation of the export is complete or not, this being done asynchronously after the creation of the object DataExport via API */
    protected bool $complete;

    /** @var int Export creation date/time in timestamp format */
    protected int $created;

    /** @var int Date/time of the beginning of the period taken into account for the export in timestamp format */
    protected int $start;

    /** @var int Date/time of the end of the period taken into account for the export in timestamp format */
    protected int $end;

    /** @var string Export ID */
    protected string $id;

    /** @var string Export type (payments, pos_export, statement, accounting, accounting_for_payout) */
    protected string $type;

    /** @var int Timestamp */
    protected int $updated;

    /**
     * URLs of files available for download for this export.
     * Not all formats are available for all export types: see details in the table below.
     * These URLs will only be filled in for the formats supported by the type of export and once the attribute completed will be true.
     */

    /** @var string CSV url of the export if available */
    protected string $csvUrl;

    /** @var string PDF url of the export if available */
    protected string $pdfUrl;

    /** @var string XSLX url of the export if available */
    protected string $xlsxUrl;

    /** @var string XML url of the export if available */
    protected string $xmlUrl;

    /** @var string ZIP url of the export if available */
    protected string $zipUrl;

    /** Mapping of required fields */
    protected array $requiredFields = [
    ];

    /** Mapping of optional fields */
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

    /**
     * Returns the data export completion status.
     * @return bool
     * @noinspection PhpUnused Used by implementations
     */
    public function isComplete(): bool
    {
        return $this->complete;
    }

    /**
     * Returns the data export creation timestamp.
     * @return int
     * @noinspection PhpUnused Used by implementations
     */
    public function getCreated(): int
    {
        return $this->created;
    }

    /**
     * Returns the data export end timestamp.
     * @return int
     * @noinspection PhpUnused Used by implementations
     */
    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * Returns the data export ID.
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Returns the data export start timestamp.
     * @return int
     * @noinspection PhpUnused Used by implementations
     */
    public function getStart(): int
    {
        return $this->start;
    }

    /**
     * Returns the data export type.
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Returns the data export update timestamp.
     * @return int
     * @noinspection PhpUnused Used by implementations
     */
    public function getUpdated(): int
    {
        return $this->updated;
    }

    /**
     * Returns the data export CSV URL.
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getCsvUrl(): string
    {
        return $this->csvUrl;
    }

    /**
     * Returns the data export PDF URL.
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getPdfUrl(): string
    {
        return $this->pdfUrl;
    }

    /**
     * Returns the data export XLSX URL.
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getXlsxUrl(): string
    {
        return $this->xlsxUrl;
    }

    /**
     * Returns the data export XML URL.
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getXmlUrl(): string
    {
        return $this->xmlUrl;
    }

    /**
     * Returns the data export ZIP URL.
     * @return string
     * @noinspection PhpUnused Used by implementations
     */
    public function getZipUrl(): string
    {
        return $this->zipUrl;
    }
}
