<?php

namespace Alma\Client\Tests\Integration\Application\Endpoint;

use Alma\Client\Application\Endpoint\DataExportEndpoint;
use Alma\Client\Domain\Entity\DataExport;

class DataExportEndpointTest extends AbstractEndpointTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->endpoint = new DataExportEndpoint($this->almaClient);
    }
    public function testCreateDataExport():string
    {
         $response = ($this->endpoint->create('payments'));
         $this->assertInstanceOf(DataExport::class, $response);
         $dataExportId = $response->getId();
         $this->assertStringStartsWith('export_', $dataExportId);
        return $dataExportId;
    }

    /**
     * @depends testCreateDataExport
     */
    public function testFetchDataExport(string $dataExportId):string
    {
         $response = ($this->endpoint->fetch($dataExportId));
         $this->assertInstanceOf(DataExport::class, $response);
         $this->assertSame($dataExportId, $response->getId());
         return $dataExportId;
    }

    // need delay to test download
}