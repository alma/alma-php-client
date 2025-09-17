<?php

namespace Alma\API\Domain\Repository;

interface GatewayRepositoryInterface {
    public function getAlmaGateways(): array;
}
