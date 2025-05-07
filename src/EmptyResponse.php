<?php

namespace Alma\API;

class EmptyResponse extends AbstractResponse
{
    public function __construct()
    {
        $this->errorMessage = null;
        $this->json = [];
        $this->responseCode = null;
    }
}
