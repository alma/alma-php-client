<?php

namespace Alma\API\Infrastructure\Repository;

use Alma\API\Domain\Adapter\ProductAdapterInterface;

interface ProductRepositoryInterface {
    public function findById(int $productId): ProductAdapterInterface;
}
