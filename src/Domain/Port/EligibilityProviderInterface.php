<?php

namespace Alma\API\Domain\Port;

use Alma\API\Domain\Entity\EligibilityList;

interface EligibilityProviderInterface
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
