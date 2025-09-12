<?php

namespace Alma\API\Tests\Integration\TestHelpers;

use Alma\API\Infrastructure\ClientConfiguration;
use Alma\API\Infrastructure\CurlClient;

class ClientTestHelper
{
    public static function getAlmaClient(): CurlClient
    {
        $config = new ClientConfiguration(['mode' => 'test', 'api_root' => $_ENV['ALMA_API_ROOT'], 'force_tls' => false]);
        return new CurlClient($config);
    }
}