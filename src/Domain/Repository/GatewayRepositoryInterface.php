<?php

namespace Alma\API\Domain\Repository;

interface GatewayRepositoryInterface {
    public function findAllAlmaGateways(): array;
}
