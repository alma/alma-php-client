<?php

namespace Alma\API\Domain\Helper;

interface AdminNotificationHelperInterface
{
    public static function notifyError( string $message ): void;
    public static function notifyWarning( string $message ): void;
    public static function notifyInfo( string $message ) : void;
    public static function notifySuccess( string $message ): void;
}
