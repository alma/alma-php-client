<?php

namespace Alma\API;

class RequestFactory implements RequestFactoryInterface
{
    public function create(ClientContext $clientContext, string $path): Request
    {
        return new Request($clientContext, $path);
    }
}