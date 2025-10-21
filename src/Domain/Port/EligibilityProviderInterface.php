<?php

namespace Alma\API\Domain\Port;

use Alma\API\Application\DTO\EligibilityDto;
use Alma\API\Domain\Entity\EligibilityList;

interface EligibilityProviderInterface
{
    /**
     * Retrieve the eligibility list based on the current cart total.
     */
    public function retrieveEligibility(EligibilityDto $eligibilityDto) : void;

    /**
     * Get the eligibility list.
     */
    public function getEligibilityList(EligibilityDto $eligibilityDto): EligibilityList;
}
