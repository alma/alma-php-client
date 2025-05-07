<?php

namespace Alma\API;

interface ClientInterface {

    public function getContext(): ClientContext;

    public function addUserAgentComponent($component, $version);
}
