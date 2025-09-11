<?php

namespace Alma\API\Domain\Helper;

interface SessionHelperInterface
{
    public function getSession( string $key, $default_session = null );
    public function setSession( string $key, $value ): void;
}
