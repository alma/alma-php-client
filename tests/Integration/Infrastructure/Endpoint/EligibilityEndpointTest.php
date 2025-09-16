<?php

namespace Alma\API\Tests\Integration\Infrastructure\Endpoint;

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
         $response = ($this->endpoint->getEligibilityList(
             ['purchase_amount' => 10000]
         ));
        $this->assertInstanceOf(EligibilityList::class, $response);
    }

}