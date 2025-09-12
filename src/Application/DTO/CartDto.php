<?php

namespace Alma\API\Application\DTO;

class CartDto implements DtoInterface {
    private array $items = [];

    public function addItem(CartItemDto $item): self {
        $this->items[] = $item->toArray();
        return $this;
    }

    /**
     * Convert the Dto to an array.
     * This method prepares the DTO for serialization or API requests.
     *
     * @return array
     */
    public function toArray(): array {
        return ['items' => $this->items];
    }
}
