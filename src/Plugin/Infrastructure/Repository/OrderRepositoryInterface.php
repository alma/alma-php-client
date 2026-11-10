<?php

namespace Alma\Plugin\Infrastructure\Repository;

use Alma\Plugin\Infrastructure\Adapter\OrderAdapterInterface;

interface OrderRepositoryInterface {
    public function getById(int $orderId): OrderAdapterInterface;
}
