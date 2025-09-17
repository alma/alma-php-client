<?php

namespace Alma\API\Domain\Helper;

interface AdminHelperInterface
{
    public static function canManageAlmaError(string $customMessage): void;
}
