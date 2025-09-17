<?php

namespace Alma\API\Domain\Repository;

interface ProductCategoryRepositoryInterface
{
    /**
     * Get the product categories.
     *
     * @return array The product categories
     */
    public function getAll(): array;
}
