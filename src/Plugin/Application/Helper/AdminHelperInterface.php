<?php

namespace Alma\Plugin\Application\Helper;

interface AdminHelperInterface
{
    public static function canManageAlmaError(string $customMessage): void;
}
