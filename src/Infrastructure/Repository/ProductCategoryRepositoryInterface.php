<?php

namespace Alma\API\Infrastructure\Repository;

interface ProductCategoryRepositoryInterface
{
    /**
     * Get the product categories.
     *
     * @return array The product categories
     */
    public static function getProductCategories(): array;
}
