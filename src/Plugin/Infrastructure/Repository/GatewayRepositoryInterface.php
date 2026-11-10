<?php

namespace Alma\Plugin\Infrastructure\Repository;

interface GatewayRepositoryInterface {
    public function findOrderedAlmaGateways(): array;
}
