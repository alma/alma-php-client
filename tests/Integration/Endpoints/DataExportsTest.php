<?php

namespace Alma\API\Tests\Integration\Endpoints;

use Alma\API\Exceptions\ParametersException;
use Alma\API\Tests\Integration\TestHelpers\ClientTestHelper;
use Mockery\Adapter\Phpunit\MockeryTestCase;

final class DataExportsTest extends MockeryTestCase
{
    protected static $almaClient;

    /**
     * @throws ParametersException
     */
    public static function setUpBeforeClass(): void
    {
        DataExportsTest::$almaClient = ClientTestHelper::getAlmaClient();
    }

    public function testCanFetchDataExport()
    {
        $data = [
            "type" => "payments",
            "include_child_accounts" => false
        ];
        $dataExport = DataExportsTest::$almaClient->dataExports->create($data);
        $this->assertNotNull($dataExport->id);

        for ($i = 0; $i < 5; $i++) {
            $fetchedExport = DataExportsTest::$almaClient->dataExports->fetch($dataExport->id);
            if ($fetchedExport->complete) {
                break;
            }
            sleep(2);
        }
        $this->assertEquals($dataExport->id, $fetchedExport->id);

        $downloadedCsvExport = DataExportsTest::$almaClient->dataExports->download($dataExport->id, 'csv');
        $this->assertNotNull($downloadedCsvExport);
    }

}
