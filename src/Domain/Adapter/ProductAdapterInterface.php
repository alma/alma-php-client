<?php

namespace Alma\API\Domain\Adapter;

interface ProductAdapterInterface
{
    public function __call( string $name, array $arguments );

    public function getPrice(): float;

    public function getCategoryIds(): array;
}
