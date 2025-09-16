<?php

namespace Alma\API\Tests\Integration\Infrastructure\Endpoint;

use Alma\API\Infrastructure\CurlClient;
use Alma\API\Infrastructure\Endpoint\AbstractEndpoint;
use Alma\API\Tests\Integration\ClientTestHelper;
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