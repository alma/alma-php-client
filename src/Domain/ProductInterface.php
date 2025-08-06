<?php

namespace Alma\API\Domain;

interface ProductInterface
{
    public function __call( string $name, array $arguments );

    public function getPrice(): float;

    public function getCategoryIds(): array;
}