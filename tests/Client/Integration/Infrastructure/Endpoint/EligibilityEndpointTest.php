<?php

namespace Alma\Client\Tests\Integration\Application\Endpoint;

use Alma\Client\Application\DTO\EligibilityDto;
use Alma\Client\Application\Endpoint\EligibilityEndpoint;
use Alma\Client\Domain\Entity\EligibilityList;

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