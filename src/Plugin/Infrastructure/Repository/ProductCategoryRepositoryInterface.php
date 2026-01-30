<?php

namespace Alma\Plugin\Infrastructure\Repository;

interface ProductCategoryRepositoryInterface
{
    /**
     * Get the product categories.
     *
     * @return array The product categories
     */
    public function getAll(): array;
}
