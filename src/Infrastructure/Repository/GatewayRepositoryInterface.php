<?php

namespace Alma\API\Infrastructure\Repository;

interface GatewayRepositoryInterface {
    public function getAlmaGateways(): array;
}
