<?php

namespace Alma\API\Helper;

class IntegrationsConfigurationsHelper
{
    /**
     * @param int | null $lastSendTimestamp
     * @return bool
     */
    public static function isUrlRefreshRequired(?int $lastSendTimestamp): bool
    {
        $oneMonthInSeconds = 30 * 24 * 60 * 60; // 30 jours en sec
        return (time() - $lastSendTimestamp) > $oneMonthInSeconds;
    }
}
