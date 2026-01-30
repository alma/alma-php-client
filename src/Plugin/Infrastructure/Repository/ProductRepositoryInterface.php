<?php

namespace Alma\Plugin\Infrastructure\Repository;

use Alma\Plugin\Infrastructure\Adapter\ProductAdapterInterface;

interface ProductRepositoryInterface {
    public function getById(int $productId): ProductAdapterInterface;
}
