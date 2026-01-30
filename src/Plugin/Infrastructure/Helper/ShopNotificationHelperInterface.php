<?php

namespace Alma\Plugin\Infrastructure\Helper;

interface ShopNotificationHelperInterface
{
    public static function notifyError( string $message ): void;
    public static function notifyInfo( string $message ) : void;
    public static function notifySuccess( string $message ): void;
}
