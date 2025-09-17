<?php

namespace Alma\API\Tests\Integration\Infrastructure\Endpoint;

use Alma\API\Domain\Entity\DataExport;
use Alma\API\Infrastructure\Endpoint\DataExportEndpoint;

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