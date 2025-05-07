<?php

namespace Alma\API;

interface RequestFactoryInterface
{
    public function create(ClientContext $clientContext, string $path): Request;
}