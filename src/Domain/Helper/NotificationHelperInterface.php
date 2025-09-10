<?php

namespace Alma\API\Domain\Helper;

interface NotificationHelperInterface
{
    public function notifyError( string $message ): void;
    public function notifyNotice( string $message ) : void;
    public function notifySuccess( string $message ): void;
}
