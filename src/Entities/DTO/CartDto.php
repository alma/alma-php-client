<?php

namespace Alma\API\Entities\DTO;

class CartDto {
    private array $items = [];

    public function addItem(CartItemDto $item): self {
        $this->items[] = $item->toArray();
        return $this;
    }

    public function toArray(): array {
        return ['items' => $this->items];
    }
}
