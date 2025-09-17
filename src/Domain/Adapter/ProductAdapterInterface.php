<?php

namespace Alma\API\Domain\Adapter;

interface ProductAdapterInterface
{
    public function __call( string $name, array $arguments );

    /**
     * Get the product ID.
     *
     * @return int The product ID.
     */
    public function getId() : int;

    /**
     * Get the product price in cents.
     *
     * @return int The product price in cents.
     */
    public function getPrice(): int;

    /**
     * Get the category IDs associated with the product.
     *
     * @return array An array of category IDs.
     */
    public function getCategoryIds(): array;
}
