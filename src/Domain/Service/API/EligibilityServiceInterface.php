<?php

namespace Alma\API\Domain\Service\API;

use Alma\API\Entity\EligibilityList;

interface EligibilityServiceInterface
{
    /**
     * Retrieve the eligibility list based on the current cart total.
     */
    public function retrieveEligibility() : void;

    /**
     * Get the eligibility list.
     */
    public function getEligibilityList(): EligibilityList;
}
