<?php

namespace Alma\Client\Tests\Integration\Application\Endpoint;

use Alma\Client\Application\Endpoint\ConfigurationEndpoint;

class ConfigurationEndpointTest extends AbstractEndpointTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->endpoint = new ConfigurationEndpoint($this->almaClient);
    }
    public function testPutUrlNotThrowException():void
    {
         $this->assertNull($this->endpoint->sendIntegrationsConfigurationsUrl('https://example.com/webhook'));
    }

}