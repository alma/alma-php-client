<?php

namespace Alma\API\DTO;

use InvalidArgumentException;

class CartItemDto implements DtoInterface {
    private ?string $sku;
    private ?string $title;
    private int $quantity;
    private ?int $unitPrice;
    private int $linePrice;
    private array $categories = [];
    private ?string $url;
    private string $pictureUrl;
    private ?bool $requiresShipping;

    public function __construct(
        int $quantity,
        int $linePrice,
        string $pictureUrl
    ) {
        $this->setQuantity($quantity);
        $this->setLinePrice($linePrice);
        $this->setPictureUrl($pictureUrl);
    }

    public function setSku(string $sku): self {
        $this->sku = $sku;
        return $this;
    }

    public function setTitle(string $title): self {
        $this->title = $title;
        return $this;
    }

    public function setQuantity(int $quantity): self {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Quantity must be positive.");
        }
        $this->quantity = $quantity;
        return $this;
    }

    public function setUnitPrice(int $unitPrice): self {
        if ($unitPrice < 0) {
            throw new InvalidArgumentException("Unit price cannot be negative.");
        }
        $this->unitPrice = $unitPrice;
        return $this;
    }
    public function setLinePrice(int $linePrice): self {
        if ($linePrice < 0) {
            throw new InvalidArgumentException("Line price cannot be negative.");
        }
        $this->linePrice = $linePrice;
        return $this;
    }
    public function setCategories(array $categories): self {
        $this->categories = $categories;
        return $this;
    }
    public function setUrl(string $url): self {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid URL format.");
        }
        $this->url = $url;
        return $this;
    }

    public function setPictureUrl(string $pictureUrl): self {
        if (!filter_var($pictureUrl, FILTER_VALIDATE_URL)) {
            throw new InvalidArgumentException("Invalid URL format.");
        }
        $this->pictureUrl = $pictureUrl;
        return $this;
    }

    public function setRequiresShipping(bool $requiresShipping): self {
        $this->requiresShipping = $requiresShipping;
        return $this;
    }

    /**
     * Convert the Dto to an array.
     * This method prepares the DTO for serialization or API requests.
     *
     * @return array
     */
    public function toArray(): array {
        return array_filter([
            'sku'               => $this->sku,
            'title'             => $this->title,
            'quantity'          => $this->quantity,
            'unit_price'        => $this->unitPrice,
            'line_price'        => $this->linePrice,
            'categories'        => $this->categories,
            'url'               => $this->url,
            'picture_url'       => $this->pictureUrl,
            'requires_shipping' => $this->requiresShipping
        ], function($value) {
            return $value !== null && $value !== '' && !(is_array($value) && empty($value));
        });
    }
}
