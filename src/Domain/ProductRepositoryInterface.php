<?php

namespace Alma\API\Domain;

interface ProductRepositoryInterface {
    public function findById(int $productId): ProductInterface;
}