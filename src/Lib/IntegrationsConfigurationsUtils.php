<?php

namespace Alma\API\Lib;

class IntegrationsConfigurationsUtils
{
    /**
     * @param int | null $lastSendTimestamp
     * @return bool
     */
    public static function isUrlRefreshRequired($lastSendTimestamp)
    {
        $oneMonthInSeconds = 30 * 24 * 60 * 60; // 30 jours en sec
        return (time() - $lastSendTimestamp) > $oneMonthInSeconds;
    }
}