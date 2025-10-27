<?php

namespace Alma\API\Tests\Integration\Infrastructure\Endpoint;

use Alma\API\Application\DTO\EligibilityDto;
use Alma\API\Domain\Entity\EligibilityList;
use Alma\API\Infrastructure\Endpoint\EligibilityEndpoint;

class EligibilityEndpointTest extends AbstractEndpointTest
{
    public function setUp(): void
    {
        parent::setUp();
        $this->endpoint = new EligibilityEndpoint($this->almaClient);
    }

    public function testEligibilityList():void
    {
        $eligibilityDto = new EligibilityDto(10000);
        $response = ($this->endpoint->getEligibilityList($eligibilityDto));
        $this->assertInstanceOf(EligibilityList::class, $response);
    }

}