<?php

namespace Alma\Client\Tests\Integration\Application\Endpoint;

use Alma\Client\Application\CurlClient;
use Alma\Client\Application\Endpoint\AbstractEndpoint;
use Alma\Client\Tests\Integration\ClientTestHelper;
use PHPUnit\Framework\TestCase;

abstract class AbstractEndpointTest extends TestCase
{
    protected ?CurlClient $almaClient;
    protected ?AbstractEndpoint $endpoint;

    public function setUp(): void
    {
        $this->almaClient = ClientTestHelper::getAlmaClient();
    }
    public function tearDown(): void
    {
        $this->almaClient = null;
        $this->endpoint = null;
    }
}