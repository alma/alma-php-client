<?php

namespace Alma\API\Domain\Repository;

use Alma\API\Domain\Adapter\ProductAdapterInterface;

interface ProductRepositoryInterface {
    public function getById(int $productId): ProductAdapterInterface;
}
