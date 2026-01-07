<?php

namespace Alma\API\Domain\Helper;

interface ShopNotificationHelperInterface
{
    public static function notifyError( string $message ): void;
    public static function notifyInfo( string $message ) : void;
    public static function notifySuccess( string $message ): void;
}
