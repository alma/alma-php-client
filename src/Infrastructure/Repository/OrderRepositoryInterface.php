<?php

namespace Alma\API\Infrastructure\Repository;

use Alma\API\Domain\Adapter\OrderAdapterInterface;

interface OrderRepositoryInterface {
    public function findById(int $orderId): OrderAdapterInterface;
}
