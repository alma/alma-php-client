<?php

namespace Alma\API\Domain\Repository;

use Alma\API\Domain\Adapter\OrderAdapterInterface;

interface OrderRepositoryInterface {
    public function getById(int $orderId): OrderAdapterInterface;
}
