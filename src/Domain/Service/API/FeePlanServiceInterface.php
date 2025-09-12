<?php

namespace Alma\API\Domain\Service\API;

use Alma\API\Domain\Entity\FeePlanList;

interface FeePlanServiceInterface
{
    /**
     * Get the fee plan list.
     *
     * @param bool $forceRefresh Whether to force a refresh of the fee plan list.
     *
     * @return FeePlanList
     */
    public function getFeePlanList( bool $forceRefresh = false ): FeePlanList;
}
